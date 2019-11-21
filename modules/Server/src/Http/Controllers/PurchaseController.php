<?php

namespace GuoJiangClub\Activity\Server\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\Activity\Core\Discount\Services\DiscountService;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Models\ActivityRefundLog;
use GuoJiangClub\Activity\Core\Models\Discount\Coupon;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Models\Payment;
use GuoJiangClub\Activity\Core\Models\Answer;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use GuoJiangClub\Activity\Server\Services\ActivityService;
use GuoJiangClub\Activity\Core\Models\Refund;
use iBrand\Component\Discount\Applicators\DiscountApplicator;
use iBrand\Component\Point\Repository\PointRepository;
use Illuminate\Events\Dispatcher;
use DB;

class PurchaseController extends Controller
{
	protected $activity;
	protected $pointRepository;
	protected $discountService;
	protected $discountApplicator;
	protected $member;
	protected $payment;
	protected $activityService;

	public function __construct(
		ActivityRepository $activityRepository,
		PointRepository $pointRepository,
		DiscountService $discountService,
		DiscountApplicator $discountApplicator,
		MemberRepository $memberRepository,
		PaymentRepository $paymentRepository,
		Dispatcher $event,
		ActivityService $activityService)
	{
		$this->activity           = $activityRepository;
		$this->pointRepository    = $pointRepository;
		$this->discountService    = $discountService;
		$this->discountApplicator = $discountApplicator;
		$this->member             = $memberRepository;
		$this->payment            = $paymentRepository;
		$this->event              = $event;
		$this->activityService    = $activityService;
	}

	public function index($id)
	{
		$activity = $this->activity->with('payments')->find($id);
		if (!$activity) {
			return $this->api([], false, 500, '活动不存在.');
		}

		if ($activity->fee_type != 'CHARGING' && $activity->member_limit != null && $activity->member_limit <= $activity->member_count) {
			return $this->api([], false, 500, '报名人数已满.');
		}

		$user    = request()->user();
		$member  = new Member(['activity_id' => $activity->id]);
		$coupons = $this->discountService->getEligibilityCoupons($member, $user->id);
		$coupons = empty($coupons) ? [] : $coupons;

		$price = 0;
		$point = 0;
		if ($activity->fee_type == 'OFFLINE_CHARGES') {
			$price   = $activity->payments()->first()->price;
			$coupons = [];
		}

		return $this->api([
			'activity' => $activity,
			'user'     => $user,
			'size'     => $user->size,
			'coupon'   => $coupons,
			'point'    => $point,
			'price'    => $price,
		]);
	}

	public function checkout($id)
	{
		$activity = $this->activity->with('payments')->find($id);
		if (!$activity) {
			return $this->api([], false, 500, '活动不存在.');
		}

		if ($activity->status != 1) {
			return $this->api([], false, 500, '当前时间无法报名.');
		}

		if (empty(request('payment_id'))) {
			return $this->api([], false, 500, '无效的支付方式');
		}

		$payment = null;
		if ($activity->payments()->first()) {
			$payment = $this->payment->find(request('payment_id'));
			if (!$payment) {
				return $this->api([], false, 500, '无效的支付方式');
			}
		} else {
			$payment = 0;
		}

		if ($activity->fee_type != 'CHARGING' && $activity->member_limit != null && $activity->member_limit <= $activity->member_count) {
			return $this->api([], false, 500, '报名人数已满.');
		} else {
			if ($activity->fee_type == 'CHARGING' && $payment->is_limit == 1 && $payment->limit <= 0) {
				return $this->api([], false, 500, '报名人数已满');
			}

			if ($activity->fee_type == 'CHARGING' && $payment->is_limit == 0 && $activity->member_limit != null && $activity->member_limit <= $activity->member_count) {
				return $this->api([], false, 500, '报名人数已满.');
			}
		}

		$has_form = false;
		if ($activity->form AND $activity->form->fields AND $activity->fee_type != 'OFFLINE_CHARGES') {
			$formFields = json_decode($activity->form->fields, true);
			foreach ($formFields as $field) {
				if ($field['status'] == 1 AND $field['is_necessary'] == 1) {
					$has_form = true;
					break;
				}
			}
		}

		$activityForm = request('activityForm');
		if ($has_form && (empty($activityForm) || !is_array($activityForm))) {
			return $this->api([], false, 500, '请完善表单信息.');
		}

		$user = request()->user();
		//首先检查是否已经报名过
		$checkout = $this->member->findWhere([['user_id', '=', $user->id], ['activity_id', '=', $id], ['status', '<>', 3]])->first();
		if ($checkout) {
			return $this->api([], false, 500, '请勿重复报名.');
		}

		$coupon     = null;
		$pay_status = 0;
		$point      = 0;
		//如果是通行证的支付方式
		if ($activity->isPassType()) {
			//1. 目前都是野练的通行证的，因此需要去检查coupon_id
			if (empty(request('coupon_id'))) {
				return $this->api([], false, 500, '请提交有效的通行证');
			}

			if (empty($coupon = Coupon::find(request('coupon_id')))) {
				return $this->api([], false, 500, '请提交有效的通行证');
			}

			if ($coupon->used_at != null) {
				return $this->api([], false, 500, '此优惠券已被使用');
			}

			$member = new Member(['activity_id' => $activity->id]);
			if ($user->id != $coupon->user_id || !$this->discountService->checkCoupon($member, $coupon)) {
				return $this->api([], false, 500, '优惠券信息有误，请确认后重试');
			}
		} elseif ($activity->isPassType(4)) {
			//如果线下现金支付,暂时不做任何操作

		} else {
			$point = $this->pointRepository->getSumPointValid($user->id);
		}

		DB::beginTransaction();

		try {

			$order_no = build_order_no('AC');
			if ($activity->isPassType()) {
				$payment = $activity->isPassType();
				//如果是通行证的支付方式
				$member = $this->member->create([
					'order_no'    => $order_no,
					'user_id'     => $user->id,
					'activity_id' => $activity->id,
					'status'      => 1,
					'pay_status'  => 1,
					'joined_at'   => Carbon::now(),
					'payment_id'  => $payment->id,
				]);

				if ($coupon) {
					$this->discountApplicator->apply($member, $coupon);
				}

				$pay_status = 1;
				$activity->update(['member_count' => $activity->member_count + 1]);
			} elseif ($activity->isPassType(4)) {
				$payment = $activity->isPassType(4);
				//如果是线下现金支付
				$member = $this->member->create([
					'order_no'    => $order_no,
					'user_id'     => $user->id,
					'activity_id' => $activity->id,
					'joined_at'   => Carbon::now(),
					'status'      => 4,
					'pay_status'  => 1,
					'payment_id'  => $payment->id,
					'price'       => $payment->price * 100,
				]);

				$pay_status = 1;
				$activity->update(['member_count' => $activity->member_count + 1]);
			}

			if (is_int($payment) && $payment === 0) {
				//免费活动
				$member     = $this->member->create([
					'order_no'    => $order_no,
					'user_id'     => $user->id,
					'status'      => 1,
					'pay_status'  => 1,
					'activity_id' => $activity->id,
					'joined_at'   => Carbon::now(),
				]);
				$pay_status = 1;
				$activity->update(['member_count' => $activity->member_count + 1]);
			} elseif ($payment->type == 0) {
				//仅积分
				if ($payment->point > $point) {
					DB::rollBack();

					return $this->api([], false, 500, '积分不够.');
				}

				$member = $this->member->create([
					'order_no'    => $order_no,
					'user_id'     => $user->id,
					'activity_id' => $activity->id,
					'status'      => 1,
					'pay_status'  => 1,
					'joined_at'   => Carbon::now(),
					'payment_id'  => $payment->id,
					'point'       => $payment->point,
				]);

				$this->pointRepository->create([
					'user_id'    => $user->id,
					'action'     => 'activity',
					'note'       => '活动报名',
					'value'      => (-1) * $payment->point,
					'valid_time' => 0,
					'item_type'  => Payment::class,
					'item_id'    => $payment->id,
				]);

				event('point.change', $user->id);
				$activity->update(['member_count' => $activity->member_count + 1]);
				$pay_status = 1;


				if ($payment->limit > 0 && $payment->is_limit == 1) {
					$payment->update(['limit' => $payment->limit - 1]);
				}
			} elseif ($payment->type == 1) {
				//仅金额
				$member = $this->member->create([
					'order_no'    => $order_no,
					'user_id'     => $user->id,
					'activity_id' => $activity->id,
					'status'      => 0,
					'joined_at'   => Carbon::now(),
					'payment_id'  => $payment->id,
					'price'       => $payment->price * 100,
					'total'       => $payment->price * 100,
					'point'       => $payment->point,
				]);
			} elseif ($payment->type == 2) {
				//积分+金额
				if ($payment->point > $point) {
					DB::rollBack();

					return $this->api([], false, 500, '积分不够.');
				}

				$member = $this->member->create([
					'order_no'    => $order_no,
					'user_id'     => $user->id,
					'activity_id' => $activity->id,
					'status'      => 0,
					'joined_at'   => Carbon::now(),
					'payment_id'  => $payment->id,
					'point'       => $payment->point,
					'price'       => $payment->price * 100,
					'total'       => $payment->price * 100,
				]);

				$pay_status = 0;
			} elseif ($payment->type == 5) {
				//收费活动 免费票
				$member     = $this->member->create([
					'order_no'    => $order_no,
					'user_id'     => $user->id,
					'status'      => 1,
					'pay_status'  => 1,
					'payment_id'  => $payment->id,
					'activity_id' => $activity->id,
					'joined_at'   => Carbon::now(),
				]);
				$pay_status = 1;
				$activity->update(['member_count' => $activity->member_count + 1]);
				if ($payment->limit > 0 && $payment->is_limit == 1) {
					$payment->update(['limit' => $payment->limit - 1]);
				}
			}

			if ($has_form && !empty($activityForm)) {
				$checkForm = $this->validateActivityForm($activityForm, $activity->form->fields);
				if (!$checkForm['status']) {
					DB::rollBack();

					return $this->api([], false, 500, $checkForm['message']);
				}

				$answer      = json_encode($activityForm);
				$checkAnswer = Answer::where('activity_id', $id)->where('user_id', $user->id)->where('order_id', $member->id)->first();
				if ($checkAnswer) {
					Answer::where('id', $checkAnswer->id)->update(['answer' => $answer]);
				} else {
					Answer::create([
						'activity_id' => $id,
						'order_id'    => $member->id,
						'user_id'     => $user->id,
						'answer'      => $answer,
					]);
				}
			}

			DB::commit();
			if ((is_int($payment) && $payment == 0) || ($payment->type != 1 && $payment->type != 2)) {
				event('on.member.activity.status.change', [$user->id, $activity, 'act_join']);
			}

			event('activity.agent.relation', [$activity, $user->id]);

			return $this->api([
				'order_no'   => $order_no,
				'user_id'    => $user->id,
				'pay_status' => $pay_status,
				'activity'   => $activity,
				'point'      => 0,
				'pointUsed'  => 0,
			]);
		} catch (\Exception $exception) {
			DB::rollBack();
			\Log::info($exception->getMessage() . $exception->getTraceAsString());

			return $this->failed('报名失败，请稍后重试');
		}
	}

	public function validateActivityForm($activityForm, $activityFormFields)
	{
		$status  = true;
		$message = '';

		if ($activityFormFields) {
			$formFields = json_decode($activityFormFields, true);
			foreach ($formFields as $formField) {
				if (($formField['status'] == 0 || $formField['status'] == 1) && $formField['is_necessary'] == 0) {
					continue;
				}

				if ($formField['name'] == 'id_card' && isset($activityForm['certificate_type']) && $activityForm['certificate_type'] == 'id_card' && (!isset($activityForm['id_card']) || !$activityForm['id_card'])) {
					$status  = false;
					$message = '请填写身份证号';

					break;
				} elseif ($formField['name'] == 'id_card' && !isset($activityForm['certificate_type']) && (!isset($activityForm['id_card']) || !$activityForm['id_card'])) {
					$status  = false;
					$message = '请填写身份证号';

					break;
				} elseif ($formField['name'] == 'other_certificate' && isset($activityForm['certificate_type']) && $activityForm['certificate_type'] == 'other_certificate' && (!isset($activityForm['other_certificate']) || !$activityForm['other_certificate'])) {
					$status  = false;
					$message = '请填写其他证件号';

					break;
				} elseif ($formField['name'] == 'other_certificate' && !isset($activityForm['certificate_type']) && (!isset($activityForm['other_certificate']) || !$activityForm['other_certificate'])) {
					$status  = false;
					$message = '请填写其他证件号';

					break;
				} elseif ($formField['name'] != 'id_card' && $formField['name'] != 'other_certificate' && (!isset($activityForm[$formField['name']]) || !$activityForm[$formField['name']])) {
					$status  = false;
					$message = '请填写' . $formField['title'];

					break;
				} else {
					continue;
				}
			}
		}

		return ['status' => $status, 'message' => $message];
	}

	public function cancel($id)
	{
		$activity = Activity::find($id);
		if (!$activity) {
			return $this->api([], false, 500, '活动不存在.');
		}

		if (!$activity->canCancel()) {
			return $this->api([], false, 500, '目前无法取消报名.');
		}

		$user   = request()->user();
		$member = $activity->members()->where('user_id', $user->id)->where('role', 'user')->whereIn('status', [0, 1])->first();//0.待支付 1.已报名
		if (!$member) {

			return $this->api([], false, 500, '未报名此活动');
		}

		if ($this->cancelHandle($activity, $member, $user->id)) {

			return $this->api(['activity' => $activity], true, 200, '取消成功.');
		}

		return $this->api([], false, 500, '服务器内部错误.');
	}

	public function cancelHandle($activity, $member, $user_id)
	{
		$order = $this->member->with('payment')->findWhere(['order_no' => $member->order_no])->first();
		if (!$order->order_no || count($order) <= 0) {
			\Log::info('订单不存在');

			return false;
		}

		$user = User::find($user_id);

		try {
			DB::beginTransaction();

			if ($member->pay_status != 0 && $member->status != 0) {
				$payment = $this->payment->find($member->payment_id);
				$activity->update(['member_count' => $activity->member_count - 1]);
				if ($payment->is_limit == 1) {
					$payment->update(['limit' => $payment->limit + 1]);
				}
			}

			if ($member->payment AND $member->payment->type == 0 AND $order->status == 1) {
				//积分报名
				$this->pointRepository->create([
					'user_id'    => $user_id,
					'action'     => 'activity_refund',
					'note'       => '取消活动报名',
					'value'      => $member->point,
					'valid_time' => 0,
					'item_type'  => Payment::class,
					'item_id'    => $member->payment->id,
				]);

				event('point.change', $user_id);

			} elseif ($member->payment AND ($member->payment->type == 1 || $member->payment->type == 2)) {
				//金额  || 金额+积分
				if ($member->payment->type == 2 && $order->status == 1 && $member->pay_status != 0) {
					$this->pointRepository->create([
						'user_id'    => $user_id,
						'action'     => 'activity_refund',
						'note'       => '取消活动报名',
						'value'      => $member->point,
						'valid_time' => 0,
						'item_type'  => Payment::class,
						'item_id'    => $member->payment->id,
					]);

					event('point.change', $user_id);

				}

				if ($activity->refund_status == 1 && $member->pay_status != 0 && $member->status != 0) {
					$input           = array_filter(request()->only('content', 'reason'));
					$input['amount'] = $order->price;
					$input           = array_merge([
						'user_id'  => $user_id,
						'order_id' => $order->id,
						'status'   => 0], $input
					);
					$refund          = new Refund($input);
					$refund->save();

					ActivityRefundLog::create(['refund_id' => $refund->id, 'user_id' => $user->id, 'action' => 'create', 'note' => '用户提交退款申请']);
				}
			} elseif ($member->payment AND $member->payment->type == 3) {
				//通行证报名
				$adjustment = $member->adjustment;
				$coupon     = Coupon::find($adjustment->origin_id);
				if ($adjustment AND $adjustment->origin_type == 'coupon' AND $coupon) {
					$coupon->used_at = null;
					$coupon->save();
				} else {
					DB::rollBack();

					return false;
				}
			} else {
				//现金支付方式
			}

			$member->pay_status = Member::STATUS_CANCEL;
			$member->status     = 3;
			$member->cancel_at  = Carbon::now();
			$member->save();

			event('activity.member.cancel', [$member->id, $activity->id]);

			DB::commit();

			return true;
		} catch (\Exception $exception) {
			\Log::info($exception->getMessage());
			DB::rollBack();

			return false;
		}
	}
}

