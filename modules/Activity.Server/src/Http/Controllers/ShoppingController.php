<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Server\Http\Controllers;

use Carbon\Carbon;
use DB;
use ElementVip\Component\Discount\Applicators\DiscountApplicator;
use ElementVip\Component\Order\Models\Order;
use ElementVip\Component\Order\Processor\OrderProcessor;
use ElementVip\Component\Order\Repositories\OrderRepository;
use ElementVip\Component\Point\Repository\PointRepository;
use ElementVip\Component\User\Models\User;
use ElementVip\Notifications\PointRecord;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Models\Answer;
use GuoJiangClub\Activity\Core\Models\Discount\Coupon;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Models\Payment;
use GuoJiangClub\Activity\Core\Notifications\Join;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use GuoJiangClub\Activity\Core\Services\DiscountService;
use GuoJiangClub\Activity\Server\Services\ActivityService;
use Illuminate\Events\Dispatcher;

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
        $this->activity = $activityRepository;
        $this->pointRepository = $pointRepository;
        $this->discountService = $discountService;
        $this->discountApplicator = $discountApplicator;
        $this->member = $memberRepository;
        $this->payment = $paymentRepository;
        $this->event = $event;
        $this->activityService = $activityService;
        $this->orderProcessor = $orderProcessor;
        $this->orderRepository = $orderRepository;
    }

    public function checkout()
    {
        $id = request('activity_id');
        $user = request()->user();
        $activity = $this->activity->find($id);

        try {
            /*检测活动相关数据*/
            $this->checkActivity($activity, $user);

            $defaultAddress = null;
            $order_total = 0;

            $payment = $this->payment->find(request('payment_id'));
            $activity_total = 0;

            if ('OFFLINE_CHARGES' == $activity->fee_type) { //如果是线下支付活动，不显示自定义表单
                $formData = null;
            } else {
                $formData = $this->formFields($activity, $user);
                $activity_total = $payment->price;
            }
            $total = $activity_total + $order_total;  //用户前端展示

            $activity = $activity->toArray();
            unset($activity['content']);

            return $this->api([
                'user' => $user,
                'address' => $defaultAddress,
                'formData' => $formData,
                'activity' => $activity,
                'size' => $user->size,
                'payment' => $payment,
                'total' => $total,
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            \Log::info($e->getTraceAsString());

            return $this->api([], false, 500, $e->getMessage());
        }
    }

    private function formFields($activity, $user)
    {
        $mobile = $user->mobile;
        $data = [];
        if (isset($activity->form) && $activity->form) {
            $data = json_decode($activity->form->fields, true);
        }

        if (is_array($data) and count($data) > 0) {
            foreach ($data as &$v) {
                if ('mobile' == $v['name']) {
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

        if (1 != $activity->status) {
            throw new \Exception('当前时间无法报名');
        }

        if (empty(request('payment_id'))) {
            throw new \Exception('无效的支付方式');
        }

        if (!$payment = $this->payment->find(request('payment_id'))) {
            throw new \Exception('无效的支付方式');
        }

        if ('CHARGING' != $activity->fee_type && null != $activity->member_limit && $activity->member_limit <= $activity->member_count) {
            throw new \Exception('报名人数已满');
        }
        if ('CHARGING' == $activity->fee_type && 1 == $payment->is_limit && $payment->limit <= 0) {
            throw new \Exception('报名人数已满');
        }

        if ('CHARGING' == $activity->fee_type && 0 == $payment->is_limit && null != $activity->member_limit && $activity->member_limit <= $activity->member_count) {
            throw new \Exception('报名人数已满');
        }

        $checkout = $this->member->findWhere([['user_id', '=', $user->id], ['activity_id', '=', $activity->id], ['status', '<>', 3]])->first();
        if ($checkout) {
            throw new \Exception('请勿重复报名');
        }
    }

    public function confirm()
    {
        $user = request()->user();
        $activity = $this->activity->find(request('activity_id'));

        $this->checkActivity($activity, $user);

        $payment = $this->payment->find(request('payment_id'));

        if ($activity->form and $activity->form->fields and !request('activityForm') and 'OFFLINE_CHARGES' != $activity->fee_type) {
            $formFields = json_decode($activity->form->fields, true);
            foreach ($formFields as $field) {
                if (1 == $field['status'] and 1 == $field['is_necessary']) {
                    return $this->api([], false, 500, '请完善表单信息.');
                }
            }
        }

        try {
            DB::beginTransaction();

            /*.活动订单相关*/
            $checkoutType = $this->getCheckOutType();
            $activity_order_no = $this->build_order_no('AC');

            $total = 0;
            $member = call_user_func([$this, 'getCheckOutFrom'.$checkoutType], $activity, $activity_order_no, $user, $payment, $total);

            /*.表单数据*/
            $this->createFormAnswer($user, $activity, $member);

            DB::commit();

            if (1 == $member->pay_status) {
                event('on.member.activity.status.change', [$user->id, $activity, 'act_join']);
            }

            event('activity.agent.relation', [$activity, $user->id]);

            return $this->api([
                'order_no' => $activity_order_no,
                'user_id' => $user->id,
                'pay_status' => $member->pay_status,
                'activity' => $activity,
                'point' => 0,
                'pointUsed' => 0,
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
     * 野练pass支付 type=3.
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

        if (null != $coupon->used_at) {
            throw new \Exception('此优惠券已被使用');
        }

        $member = new Member(['activity_id' => $activity->id]);

        if ($user->id != $coupon->user_id || !$this->discountService->checkCoupon($member, $coupon)) {
            throw new \Exception('优惠券信息有误，请确认后重试');
        }

        $member = $this->member->create([
            'order_no' => $order_no,
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => $total > 0 ? 0 : 1,
            'pay_status' => $total > 0 ? 0 : 1,
            'total' => $total,
            'joined_at' => Carbon::now(),
            'payment_id' => $payment->id,
        ]);

        if ($coupon) {
            $this->discountApplicator->apply($member, $coupon);
        }

        if (0 == $total) {
            $activity->update(['member_count' => $activity->member_count + 1]);
            $user->notify(new Join(['activity' => $activity]));
        }

        return $member;
    }

    /**
     * 线下支付 type=4.
     *
     * @param $activity
     * @param $order_no
     * @param $user
     */
    private function getCheckOutFromOfflineCharge($activity, $order_no, $user, $payment, $total)
    {
        $member = $this->member->create([
            'order_no' => $order_no,
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'joined_at' => Carbon::now(),
            'status' => $total > 0 ? 0 : 4,
            'pay_status' => $total > 0 ? 0 : 1,
            'total' => $total,
            'payment_id' => $payment->id,
            'price' => $payment->price * 100,
        ]);

        if (0 == $total) {
            $activity->update(['member_count' => $activity->member_count + 1]);
        }

        return $member;
    }

    /**
     * 纯积分支付 type=0.
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
            'order_no' => $order_no,
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => $total > 0 ? 0 : 1,
            'pay_status' => $total > 0 ? 0 : 1,
            'total' => $total,
            'joined_at' => Carbon::now(),
            'payment_id' => $payment->id,
            'point' => $payment->point,
        ]);

        if (0 == $total) {
            $this->pointRepository->create([
                'user_id' => $user->id,
                'action' => 'activity',
                'note' => '活动报名',
                'value' => (-1) * $payment->point,
                'valid_time' => 0,
                'item_type' => Payment::class,
                'item_id' => $payment->id,
            ]);

            event('point.change', $user->id);

            $user->notify(new PointRecord(['point' => [
                'user_id' => $user->id,
                'action' => 'activity',
                'note' => '活动报名',
                'value' => (-1) * $payment->point,
                'valid_time' => 0,
                'item_type' => Payment::class,
                'item_id' => $payment->id,
            ]]));

            $activity->update(['member_count' => $activity->member_count + 1]);
            if ($payment->limit > 0 && 1 == $payment->is_limit) {
                $payment->update(['limit' => $payment->limit - 1]);
            }
        }

        return $member;
    }

    /**
     * 完全免费活动 type=5.
     *
     * @param $activity
     * @param $order_no
     * @param $user
     */
    private function getCheckOutFromFreeCharge($activity, $order_no, $user, $payment, $total)
    {
        $member = $this->member->create([
            'order_no' => $order_no,
            'user_id' => $user->id,
            'status' => $total > 0 ? 0 : 1,
            'pay_status' => $total > 0 ? 0 : 1,
            'total' => $total,
            'activity_id' => $activity->id,
            'joined_at' => Carbon::now(),
            'payment_id' => $payment->id,
        ]);

        if (0 == $total) {
            $activity->update(['member_count' => $activity->member_count + 1]);
            if ($payment->limit > 0 && 1 == $payment->is_limit) {
                $payment->update(['limit' => $payment->limit - 1]);
            }
        }

        return $member;
    }

    /**
     * 纯现金支付 type=1.
     *
     * @param $activity
     * @param $order_no
     * @param $user
     * @param $payment
     */
    private function getCheckOutFromCashCharge($activity, $order_no, $user, $payment, $total)
    {
        $member = $this->member->create([
            'order_no' => $order_no,
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => 0,
            'joined_at' => Carbon::now(),
            'payment_id' => $payment->id,
            'price' => $payment->price * 100,
            'total' => $payment->price * 100 + $total,
        ]);

        return $member;
    }

    /**
     * 积分+现金 type=2.
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
            'order_no' => $order_no,
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => 0,
            'joined_at' => Carbon::now(),
            'payment_id' => $payment->id,
            'point' => $payment->point,
            'price' => $payment->price * 100,
            'total' => $payment->price * 100 + $total,
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

            $answer = json_encode($activityForm);
            $checkAnswer = Answer::where('activity_id', $activity->id)->where('user_id', $user->id)->where('order_id', $member->id)->first();
            if ($checkAnswer) {
                Answer::where('id', $checkAnswer->id)->update(['answer' => $answer]);
            } else {
                Answer::create([
                    'activity_id' => $activity->id,
                    'order_id' => $member->id,
                    'user_id' => $user->id,
                    'answer' => $answer,
                ]);
            }
        }
    }

    public function validateActivityForm($activityForm, $activityFormFields)
    {
        if ($activityFormFields) {
            $formFields = json_decode($activityFormFields, true);
            foreach ($formFields as $formField) {
                if ((0 == $formField['status'] || 1 == $formField['status']) && 0 == $formField['is_necessary']) {
                    continue;
                }

                if ('id_card' == $formField['name'] && isset($activityForm['certificate_type']) && 'id_card' == $activityForm['certificate_type'] && (!isset($activityForm['id_card']) || !$activityForm['id_card'])) {
                    return ['status' => false, 'message' => '请填写身份证号'];
                    break;
                } elseif ('id_card' == $formField['name'] && !isset($activityForm['certificate_type']) && (!isset($activityForm['id_card']) || !$activityForm['id_card'])) {
                    return ['status' => false, 'message' => '请填写身份证号'];
                    break;
                } elseif ('other_certificate' == $formField['name'] && isset($activityForm['certificate_type']) && 'other_certificate' == $activityForm['certificate_type'] && (!isset($activityForm['other_certificate']) || !$activityForm['other_certificate'])) {
                    return ['status' => false, 'message' => '请填写其他证件号'];
                    break;
                } elseif ('other_certificate' == $formField['name'] && !isset($activityForm['certificate_type']) && (!isset($activityForm['other_certificate']) || !$activityForm['other_certificate'])) {
                    return ['status' => false, 'message' => '请填写其他证件号'];
                    break;
                } elseif ('id_card' != $formField['name'] && 'other_certificate' != $formField['name'] && (!isset($activityForm[$formField['name']]) || !$activityForm[$formField['name']])) {
                    return ['status' => false, 'message' => '请填写'.$formField['title']];
                    break;
                }
                continue;
            }
        }

        return ['status' => true];
    }

    private function build_order_no($prefix = 'O')
    {
        //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）

        $order_id_main = date('Ymd').rand(100000000, 999999999);

        //订单号码主体长度

        $order_id_len = strlen($order_id_main);

        $order_id_sum = 0;

        for ($i = 0; $i < $order_id_len; ++$i) {
            $order_id_sum += (int) (substr($order_id_main, $i, 1));
        }

        //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）

        $order_id = $order_id_main.str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);

        return $prefix.$order_id;
    }
}
