<?php
/**
 * For MiniProgram
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/21
 * Time: 15:19
 */

namespace GuojiangClub\Activity\Server\Http\Controllers;

use Carbon\Carbon;
use ElementVip\Activity\Core\Models\Activity;
use ElementVip\Activity\Core\Models\ActivityOrders;
use ElementVip\Activity\Core\Models\ActivityRefundLog;
use ElementVip\Activity\Core\Models\Discount\Coupon;
use ElementVip\Activity\Core\Models\Member;
use ElementVip\Activity\Core\Models\Payment;
use ElementVip\Activity\Core\Models\Answer;
use ElementVip\Activity\Core\Notifications\Join;
use ElementVip\Activity\Core\Repository\ActivityRepository;
use ElementVip\Activity\Core\Repository\MemberRepository;
use ElementVip\Activity\Core\Repository\PaymentRepository;
use ElementVip\Activity\Core\Services\DiscountService;
use GuojiangClub\Activity\Server\Services\ActivityService;
use ElementVip\Component\Address\Models\Address;
use ElementVip\Component\Discount\Applicators\DiscountApplicator;
use ElementVip\Component\Order\Models\Order;
use ElementVip\Component\Order\Models\OrderItem;
use ElementVip\Component\Order\Processor\OrderProcessor;
use ElementVip\Component\Order\Repositories\OrderRepository;
use ElementVip\Component\Point\Repository\PointRepository;
use ElementVip\Activity\Core\Models\Refund;
use ElementVip\Component\Product\Models\Goods;
use Illuminate\Events\Dispatcher;
use ElementVip\Notifications\PointRecord;
use ElementVip\Component\User\Models\User;
use DB;

class ShoppingController extends Controller
{
	protected $activity;
	protected $pointRepository;
	protected $discountService;
	protected $discountApplicator;
	protected $member;
	protected $payment;
	protected $activityService;
	protected $orderProcessor;
	protected $orderRepository;

	public function __construct(
		ActivityRepository $activityRepository,
		PointRepository $pointRepository,
		DiscountService $discountService,
		DiscountApplicator $discountApplicator,
		MemberRepository $memberRepository,
		PaymentRepository $paymentRepository,
		Dispatcher $event,
		ActivityService $activityService,
		OrderProcessor $orderProcessor,
		OrderRepository $orderRepository)
	{
		$this->activity           = $activityRepository;
		$this->pointRepository    = $pointRepository;
		$this->discountService    = $discountService;
		$this->discountApplicator = $discountApplicator;
		$this->member             = $memberRepository;
		$this->payment            = $paymentRepository;
		$this->event              = $event;
		$this->activityService    = $activityService;
		$this->orderProcessor     = $orderProcessor;
		$this->orderRepository    = $orderRepository;
	}

	public function checkout()
	{
		$id        = request('activity_id');
		$user      = request()->user();
		$activity  = $this->activity->find($id);
		$goods     = request('goods');
		$cartItems = null;
		$order     = null;

		try {
			/*检测活动相关数据*/
			$this->checkActivity($activity, $user);

			/*检测商品数据*/
			$required = $activity->goods->filter(function ($value, $key) {
				return $value->pivot->required == 1;
			});

			if ($required AND $required->count() > 0 AND !$goods) {
				throw new \Exception('请选择装备');
			}

			$defaultAddress = null;
			$order_total    = 0;

			if ($goods AND count($goods) > 0) {

				$defaultAddress = Address::getDefaultAddress($user->id);

				//1.构建购物车数据
				$cartItems = $this->activityService->makeCartItems($goods);
				foreach ($cartItems as $key => $item) {
					//检查库存是否足够
					if (!$this->checkItemStock($item)) {
						throw new \Exception('装备: ' . $item->name . ' ' . $item->color . ',' . $item->size . ' 库存数量不足');
					}
				}

				$order       = new Order(['user_id' => $user->id, 'payable_freight' => 0]);
				$order->type = Order::TYPE_ACTIVITY_TEMP; //临时类型

				//2. 生成临时订单对象
				$order = $this->BuildOrderItemsFromActivity($cartItems, $order, $id);

				if (!$order = $this->orderProcessor->create($order)) {
					throw new \Exception('订单提交失败，请确认后重试');
				}
				$order_total = $order->total_yuan;
			}

			$payment        = $this->payment->find(request('payment_id'));
			$activity_total = 0;

			if ($activity->fee_type == 'OFFLINE_CHARGES') { //如果是线下支付活动，不显示自定义表单
				$formData = null;
			} else {
				$formData       = $this->formFields($activity, $user);
				$activity_total = $payment->price;
			}
			$total = $activity_total + $order_total;  //用户前端展示

			$activity = $activity->toArray();
			unset($activity['content']);

			return $this->api([
				'user'     => $user,
				'order'    => $order,
				'address'  => $defaultAddress,
				'formData' => $formData,
				'activity' => $activity,
				'size'     => $user->size,
				'payment'  => $payment,
				'total'    => $total,
			]);
		} catch (\Exception $e) {
			\Log::info($e->getMessage());
			\Log::info($e->getTraceAsString());

			return $this->api([], false, 500, $e->getMessage());
		}
	}

	private function BuildOrderItemsFromActivity($cartItems, $order, $activity_id)
	{
		foreach ($cartItems as $key => $item) {
			if ($item->qty > 0) {
				$item_meta = [
					'image'     => $item->img,
					'detail_id' => $item->model->detail_id,
				];

				$item_meta['specs_text'] = $item->model->specs_text;

				$orderItem = new OrderItem([
					'quantity'   => $item->qty,
					'unit_price' => $this->activityService->getActivityGoodsPrice($activity_id, $item->com_id),
					'item_id'    => $item->id,
					'type'       => $item->__model,
					'item_name'  => $item->name,
					'item_meta'  => $item_meta,
				]);

				$orderItem->recalculateUnitsTotal();

				$order->addItem($orderItem);
			}
		}

		return $order;
	}

	private function formFields($activity, $user)
	{
		$mobile = $user->mobile;
		$data   = [];
		if (isset($activity->form) && $activity->form) {
			$data = json_decode($activity->form->fields, true);
		}

		if (is_array($data) AND count($data) > 0) {
			foreach ($data as &$v) {
				if ($v['name'] == 'mobile') {
					$v['value'] = $mobile;
				} else {
					$v['value'] = '';
				}
			}
		}

		if (isset($activity->statement) && $activity->statement) {
			$statement = ['status' => 1, 'is_necessary' => 1, 'type' => 'statement', 'index' => 0, 'name' => 'statement', 'title' => $activity->statement->title, 'value' => $activity->statement->statement];
			array_push($data, $statement);
		}

		return $data;
	}

	private function checkActivity($activity, $user)
	{
		if (!$activity) {
			throw new \Exception('活动不存在');
		}

		if ($activity->status != 1) {
			throw new \Exception('当前时间无法报名');
		}

		if (empty(request('payment_id'))) {
			throw new \Exception('无效的支付方式');
		}

		if (!$payment = $this->payment->find(request('payment_id'))) {
			throw new \Exception('无效的支付方式');
		}

		if ($activity->fee_type != 'CHARGING' && $activity->member_limit != null && $activity->member_limit <= $activity->member_count) {
			throw new \Exception('报名人数已满');
		} else {
			if ($activity->fee_type == 'CHARGING' && $payment->is_limit == 1 && $payment->limit <= 0) {
				throw new \Exception('报名人数已满');
			}

			if ($activity->fee_type == 'CHARGING' && $payment->is_limit == 0 && $activity->member_limit != null && $activity->member_limit <= $activity->member_count) {
				throw new \Exception('报名人数已满');
			}
		}

		$checkout = $this->member->findWhere([['user_id', '=', $user->id], ['activity_id', '=', $activity->id], ['status', '<>', 3]])->first();
		if ($checkout) {
			throw new \Exception('请勿重复报名');
		}
	}

	public function confirm()
	{
		$user     = request()->user();
		$activity = $this->activity->find(request('activity_id'));

		$this->checkActivity($activity, $user);

		$payment = $this->payment->find(request('payment_id'));

		$order_no = request('order_no');
		$order    = $this->orderRepository->getOrderByNo($order_no);
		if ($order_no AND !$order) {
			return $this->api([], false, 500, '订单不存在');
		}

		if ($order) {
			foreach ($order->getItems() as $item) { // 再次checker库存
				$model   = $item->type;
				$model   = new $model();
				$product = $model->find($item->item_id);

				if (!$product->getIsInSale($item->quantity)) {
					return $this->api([], false, 500, '商品: ' . $product->name . ' 库存不够，请重新下单');
				}
			}
		}

		if ($activity->form AND $activity->form->fields AND !request('activityForm') AND $activity->fee_type != 'OFFLINE_CHARGES') {
			$formFields = json_decode($activity->form->fields, true);
			foreach ($formFields as $field) {
				if ($field['status'] == 1 AND $field['is_necessary'] == 1) {
					return $this->api([], false, 500, '请完善表单信息.');
				}
			}
		}

		try {
			DB::beginTransaction();

			/*.活动订单相关*/
			$checkoutType      = $this->getCheckOutType();
			$activity_order_no = build_order_no('AC');

			$total = 0;
			if ($order) {
				/*.商品订单*/
				if ($address = Address::find(request('address_id'))) {
					$order->accept_name  = $address->accept_name;
					$order->mobile       = $address->mobile;
					$order->address      = $address->address;
					$order->address_name = $address->address_name;
				}
				$order->source = request('source') ? request('source') : 'mini';
				$order->save();

				$total  = $order->total;
				$member = call_user_func([$this, 'getCheckOutFrom' . $checkoutType], $activity, $activity_order_no, $user, $payment, $total);

				/*.创建活动订单与商品订单关系*/
				ActivityOrders::create(['activity_id' => $activity->id, 'order_id' => $order->id, 'member_id' => $member->id]);

				$this->orderProcessor->process($order);

				foreach ($order->getItems() as $item) {
					$model   = $item->type;
					$model   = new $model();
					$product = $model->find($item->item_id);
					$product->reduceStock($item->quantity);
					$product->increaseSales($item->quantity);
					$product->save();
				}
			} else {
				$member = call_user_func([$this, 'getCheckOutFrom' . $checkoutType], $activity, $activity_order_no, $user, $payment, $total);
			}

			/*.表单数据*/
			$this->createFormAnswer($user, $activity, $member);

			DB::commit();

			if ($member->pay_status == 1) {
				event('on.member.activity.status.change', [$user->id, $activity, 'act_join']);
			}

			event('activity.agent.relation', [$activity, $user->id]);

			return $this->api([
				'order_no'   => $activity_order_no,
				'user_id'    => $user->id,
				'pay_status' => $member->pay_status,
				'activity'   => $activity,
				'point'      => 0,
				'pointUsed'  => 0,
			]);
		} catch (\Exception $exception) {
			DB::rollBack();
			\Log::info($exception->getMessage());
			\Log::info($exception->getTraceAsString());

			return $this->api([], false, 500, $exception->getMessage());
		}
	}

	private function getCheckOutType()
	{
		$payment = $this->payment->find(request('payment_id'));

		switch ($payment->type) {
			case 0:
				return 'PointCharge';  //纯积分支付
				break;
			case 1:
				return 'CashCharge';  //纯现金支付
				break;
			case 2:
				return 'BlendCharge'; //积分+现金支付
				break;
			case 3:
				return 'PassCharge'; //野练pass支付
				break;
			case 4:
				return 'OfflineCharge'; //线下支付
				break;
			case 5:
				return 'FreeCharge'; //完全免费
				break;
			default:
				return null;
		}
	}

	/**
	 * 野练pass支付 type=3
	 *
	 * @param $activity
	 * @param $order_no
	 * @param $user
	 *
	 * @throws \Exception
	 */
	private function getCheckOutFromPassCharge($activity, $order_no, $user, $payment, $total)
	{
		$coupon = null;
		if (empty(request('coupon_id'))) {
			throw new \Exception('请提交有效的通行证');
		}

		if (empty($coupon = Coupon::find(request('coupon_id')))) {
			throw new \Exception('请提交有效的通行证');
		}

		if ($coupon->used_at != null) {
			throw new \Exception('此优惠券已被使用');
		}

		$member = new Member(['activity_id' => $activity->id]);

		if ($user->id != $coupon->user_id || !$this->discountService->checkCoupon($member, $coupon)) {
			throw new \Exception('优惠券信息有误，请确认后重试');
		}

		$member = $this->member->create([
			'order_no'    => $order_no,
			'user_id'     => $user->id,
			'activity_id' => $activity->id,
			'status'      => $total > 0 ? 0 : 1,
			'pay_status'  => $total > 0 ? 0 : 1,
			'total'       => $total,
			'joined_at'   => Carbon::now(),
			'payment_id'  => $payment->id,
		]);

		if ($coupon) {
			$this->discountApplicator->apply($member, $coupon);
		}

		if ($total == 0) {
			$activity->update(['member_count' => $activity->member_count + 1]);
			$user->notify(new Join(['activity' => $activity]));
		}

		return $member;
	}

	/**
	 * 线下支付 type=4
	 *
	 * @param $activity
	 * @param $order_no
	 * @param $user
	 */
	private function getCheckOutFromOfflineCharge($activity, $order_no, $user, $payment, $total)
	{
		$member = $this->member->create([
			'order_no'    => $order_no,
			'user_id'     => $user->id,
			'activity_id' => $activity->id,
			'joined_at'   => Carbon::now(),
			'status'      => $total > 0 ? 0 : 4,
			'pay_status'  => $total > 0 ? 0 : 1,
			'total'       => $total,
			'payment_id'  => $payment->id,
			'price'       => $payment->price * 100,
		]);

		if ($total == 0) {
			$activity->update(['member_count' => $activity->member_count + 1]);
		}

		return $member;
	}

	/**
	 * 纯积分支付 type=0
	 *
	 * @param $activity
	 * @param $order_no
	 * @param $user
	 *
	 * @throws \Exception
	 */
	private function getCheckOutFromPointCharge($activity, $order_no, $user, $payment, $total)
	{
		$point = $this->pointRepository->getSumPointValid($user->id, 'default');

		if ($payment->point > $point) {
			throw new \Exception('积分不够');
		}

		$member = $this->member->create([
			'order_no'    => $order_no,
			'user_id'     => $user->id,
			'activity_id' => $activity->id,
			'status'      => $total > 0 ? 0 : 1,
			'pay_status'  => $total > 0 ? 0 : 1,
			'total'       => $total,
			'joined_at'   => Carbon::now(),
			'payment_id'  => $payment->id,
			'point'       => $payment->point,
		]);

		if ($total == 0) {
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

			$user->notify(new PointRecord(['point' => [
				'user_id'    => $user->id,
				'action'     => 'activity',
				'note'       => '活动报名',
				'value'      => (-1) * $payment->point,
				'valid_time' => 0,
				'item_type'  => Payment::class,
				'item_id'    => $payment->id,
			]]));

			$activity->update(['member_count' => $activity->member_count + 1]);
			if ($payment->limit > 0 && $payment->is_limit == 1) {
				$payment->update(['limit' => $payment->limit - 1]);
			}
		}

		return $member;
	}

	/**
	 * 完全免费活动 type=5
	 *
	 * @param $activity
	 * @param $order_no
	 * @param $user
	 */
	private function getCheckOutFromFreeCharge($activity, $order_no, $user, $payment, $total)
	{
		$member = $this->member->create([
			'order_no'    => $order_no,
			'user_id'     => $user->id,
			'status'      => $total > 0 ? 0 : 1,
			'pay_status'  => $total > 0 ? 0 : 1,
			'total'       => $total,
			'activity_id' => $activity->id,
			'joined_at'   => Carbon::now(),
			'payment_id'  => $payment->id,
		]);

		if ($total == 0) {
			$activity->update(['member_count' => $activity->member_count + 1]);
			if ($payment->limit > 0 && $payment->is_limit == 1) {
				$payment->update(['limit' => $payment->limit - 1]);
			}
		}

		return $member;
	}

	/**
	 * 纯现金支付 type=1
	 *
	 * @param $activity
	 * @param $order_no
	 * @param $user
	 * @param $payment
	 */
	private function getCheckOutFromCashCharge($activity, $order_no, $user, $payment, $total)
	{
		$member = $this->member->create([
			'order_no'    => $order_no,
			'user_id'     => $user->id,
			'activity_id' => $activity->id,
			'status'      => 0,
			'joined_at'   => Carbon::now(),
			'payment_id'  => $payment->id,
			'price'       => $payment->price * 100,
			'total'       => $payment->price * 100 + $total,
		]);

		return $member;
	}

	/**
	 * 积分+现金 type=2
	 *
	 * @param $activity
	 * @param $order_no
	 * @param $user
	 * @param $payment
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	private function getCheckOutFromBlendCharge($activity, $order_no, $user, $payment, $total)
	{
		$point = $this->pointRepository->getSumPointValid($user->id, 'default');

		if ($payment->point > $point) {
			throw new \Exception('积分不够');
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
			'total'       => $payment->price * 100 + $total,
		]);

		return $member;
	}

	private function checkItemStock($item)
	{
		if (is_null($item->model) || !$item->model->getIsInSale($item->qty)) {
			return false;
		}

		return true;
	}

	private function createFormAnswer($user, $activity, $member)
	{
		$activityForm = request('activityForm');
		if (!empty($activityForm) && $activity->form && isset($activity->form->fields) && $activity->form->fields) {
			$checkForm = $this->validateActivityForm($activityForm, $activity->form->fields);
			if (!$checkForm['status']) {
				throw new \Exception($checkForm['message']);
			}

			$answer      = json_encode($activityForm);
			$checkAnswer = Answer::where('activity_id', $activity->id)->where('user_id', $user->id)->where('order_id', $member->id)->first();
			if ($checkAnswer) {
				Answer::where('id', $checkAnswer->id)->update(['answer' => $answer]);
			} else {
				Answer::create([
					'activity_id' => $activity->id,
					'order_id'    => $member->id,
					'user_id'     => $user->id,
					'answer'      => $answer,
				]);
			}
		}
	}

	public function validateActivityForm($activityForm, $activityFormFields)
	{
		if ($activityFormFields) {
			$formFields = json_decode($activityFormFields, true);
			foreach ($formFields as $formField) {
				if (($formField['status'] == 0 || $formField['status'] == 1) && $formField['is_necessary'] == 0) {
					continue;
				}

				if ($formField['name'] == 'id_card' && isset($activityForm['certificate_type']) && $activityForm['certificate_type'] == 'id_card' && (!isset($activityForm['id_card']) || !$activityForm['id_card'])) {
					return ['status' => false, 'message' => '请填写身份证号'];
					break;
				} elseif ($formField['name'] == 'id_card' && !isset($activityForm['certificate_type']) && (!isset($activityForm['id_card']) || !$activityForm['id_card'])) {
					return ['status' => false, 'message' => '请填写身份证号'];
					break;
				} elseif ($formField['name'] == 'other_certificate' && isset($activityForm['certificate_type']) && $activityForm['certificate_type'] == 'other_certificate' && (!isset($activityForm['other_certificate']) || !$activityForm['other_certificate'])) {
					return ['status' => false, 'message' => '请填写其他证件号'];
					break;
				} elseif ($formField['name'] == 'other_certificate' && !isset($activityForm['certificate_type']) && (!isset($activityForm['other_certificate']) || !$activityForm['other_certificate'])) {
					return ['status' => false, 'message' => '请填写其他证件号'];
					break;
				} elseif ($formField['name'] != 'id_card' && $formField['name'] != 'other_certificate' && (!isset($activityForm[$formField['name']]) || !$activityForm[$formField['name']])) {
					return ['status' => false, 'message' => '请填写' . $formField['title']];
					break;
				} else {
					continue;
				}
			}
		}

		return ['status' => true];
	}
}