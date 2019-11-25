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

use EasyWeChat;
use ElementVip\Component\Order\Models\Order;
use ElementVip\Component\Payment\Contracts\PaymentChargeContract;
use ElementVip\Component\Payment\Models\Payment;
use ElementVip\Component\Payment\Services\ActivityPaymentService;
use ElementVip\Component\Point\Repository\PointRepository;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Server\Services\ActivityService;
use Illuminate\Events\Dispatcher;
use Validator;

class ActivityPaymentController extends Controller
{
    private $payment;
    private $pointRepository;
    private $events;
    private $member;
    private $activity;
    private $charge;
    private $activityService;

    public function __construct(
        ActivityPaymentService $paymentService,
        PointRepository $pointRepository,
        Dispatcher $events,
        MemberRepository $memberRepository,
        ActivityRepository $activityRepository, PaymentChargeContract $chargeContract, ActivityService $activityService)
    {
        $this->payment = $paymentService;
        $this->pointRepository = $pointRepository;
        $this->events = $events;
        $this->member = $memberRepository;
        $this->activity = $activityRepository;
        $this->charge = $chargeContract;
        $this->activityService = $activityService;
    }

    public function createCharge()
    {
        $user = request()->user();
        $input = request()->all();

        $validator = Validator::make($input, [
            'order_no' => 'required',
            'channel' => 'required',
        ], [
            'order_no.required' => '提交支付请求失败,必填参数缺失',
            'channel.required' => '提交支付请求失败,必填参数缺失',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return $this->api([], false, 500, $errors);
        }

        $order = $this->member->with('payment')->findWhere(['order_no' => $input['order_no']])->first();
        if (!$input['order_no'] || !$order) {
            return $this->api([], false, 500, '订单不存在');
        }

        if (1 == $order['pay_status']) {
            return $this->api([], false, 500, '订单已支付');
        }

        if (Member::STATUS_INVALID == $order->status) {
            return $this->api(null, false, 500, '无法支付');
        }

        $point = $this->pointRepository->getSumPointValid($user->id, 'default');
        if ($order->point > $point) {
            return $this->api([], false, 500, '积分不够.');
        }

        $redirect_url = $this->getRedirectUrl();

        if ('wx_pub_qr' == request('channel')) {
            $activity = $this->activity->find($order->activity_id);

            $charge = $this->charge->createCharge(
                $order->user_id, request('channel'), 'activity', $input['order_no'], $order->total, $activity->id.' '.$activity->title, $activity->id.' '.$activity->title, request()->getClientIp(), '', request('extra')
            );

            return $this->api(compact('charge'));
        }

        return $this->api(compact('redirect_url'));
    }

    private function getRedirectUrl()
    {
        $type = 'activity';
        $order_no = request('order_no');
        $channel = request('channel');
        /*if (empty($channel)) {
            $channel = 'wx_pub';
        }*/

        if ('alipay_wap' == $channel) {
            return route('ali.pay.charge', compact('channel', 'type', 'order_no', 'balance'));
        }

        if ('wx_pub' == $channel) {
            return route('wechat.pay.getCode', compact('channel', 'type', 'order_no', 'balance'));
        }
    }

    public function paidSuccess()
    {
        $order_no = request('order_no');

        $order = $this->member->with('activity')->findWhere(['order_no' => $order_no])->first();
        if (!$order_no || !$order) {
            return $this->api([], false, 500, '订单不存在');
        }

        if (1 != $order->pay_status) {
            //同步查询微信订单状态，防止异步通信失败导致订单状态更新失败
            /*$payment = EasyWeChat::payment('activity');
            $result = $payment->order->queryByOutTradeNumber($order_no);

            if ('FAIL' == $result['return_code']) {
                return $this->failed($result['return_msg']);
            }

            if ('FAIL' == $result['result_code']) {
                return $this->failed($result['err_code_des']);
            }

            if ('SUCCESS' != $result['trade_state']) {
                return $this->failed($result['trade_state_desc']);
            }

            $attach = json_decode($result['attach'], true);

            $charge['metadata']['order_sn'] = $attach['order_sn'];
            $charge['metadata']['type'] = $attach['type'];
            $charge['amount'] = $result['total_fee'];
            $charge['transaction_no'] = $result['transaction_id'];
            $charge['channel'] = $attach['channel'];
            $charge['id'] = $result['out_trade_no'];
            $charge['time_paid'] = strtotime($result['time_end']);
            $charge['details'] = json_encode($result);

            $this->payment->paySuccess($charge);*/

            $result = $this->charge->queryByOutTradeNumber($order_no);
            if (count($result) > 0 and 'activity' == $result['metadata']['type']) {
                $this->payment->paySuccess($result);
            }
        }

        $order = $this->member->with('activity')->findWhere(['order_no' => $order_no])->first();
        if (!$order_no || !$order) {
            return $this->api([], false, 500, '订单不存在');
        }
        $formData = $this->activityService->getFormData($order);
        $order->formData = $formData;

        return $this->success($order, true);
    }

    /**
     * 小程序支付同步通知.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function miniPaidSuccess()
    {
        $order_no = request('order_no');

        $order = $this->member->with('activity')->findWhere(['order_no' => $order_no])->first();
        if (!$order_no) {
            return $this->api([], false, 500, '订单不存在');
        }

        if (1 != $order->pay_status) {
            $result = $this->charge->queryByOutTradeNumber($order_no);
            if (count($result) > 0 and 'activity' == $result['metadata']['type']) {
                $this->payment->paySuccess($result);
            }
        }
        $order = $this->member->with('activity')->findWhere(['order_no' => $order_no])->first();
        if (!$order_no) {
            return $this->api([], false, 500, '订单不存在');
        }
        $formData = $this->activityService->getFormData($order);
        $order->formData = $formData;

        return $this->success($order, true);
    }

    /**
     * 不需要现金支付的报名成功回调.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function freePaidSuccess()
    {
        $order_no = request('order_no');

        $order = $this->member->with('activity')->findWhere(['order_no' => $order_no])->first();
        if (!$order_no || !$order) {
            return $this->api([], false, 500, '订单不存在');
        }

        $formData = $this->activityService->getFormData($order);
        $order->formData = $formData;

        return $this->success($order, true);
    }
}
