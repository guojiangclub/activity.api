<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-10-09
 * Time: 18:41
 */

namespace GuojiangClub\Activity\Server\Http\Controllers;

use Carbon\Carbon;
use GuojiangClub\Activity\Server\Services\ActivityService;
use ElementVip\Component\Order\Models\Order;
use ElementVip\Component\Payment\Contracts\PaymentChargeContract;
use ElementVip\Component\Point\Model\Point;
use ElementVip\Component\Point\Repository\PointRepository;
use ElementVip\Activity\Core\Repository\MemberRepository;
use ElementVip\Activity\Core\Repository\ActivityRepository;
use ElementVip\Activity\Core\Models\Member;
use ElementVip\Component\Payment\Models\Payment;
use ElementVip\Component\Payment\Services\ActivityPaymentService;
use Pingpp\WxpubOAuth;
use Illuminate\Events\Dispatcher;
use Validator;
use EasyWeChat;

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
        ActivityRepository $activityRepository
        , PaymentChargeContract $chargeContract
        , ActivityService $activityService)
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

        if ($order['pay_status'] == 1) {
            return $this->api([], false, 500, '订单已支付');
        }

        if ($order->status == Member::STATUS_INVALID) {
            return $this->api(null, false, 500, '无法支付');
        }

        $point = $this->pointRepository->getSumPointValid($user->id, 'default');
        if ($order->point > $point) {
            return $this->api([], false, 500, '积分不够.');
        }

        $redirect_url = $this->getRedirectUrl();

        if (request('channel') == 'wx_pub_qr') {

            $activity = $this->activity->find($order->activity_id);

            $charge = $this->charge->createCharge(
                $order->user_id
                , request('channel')
                , 'activity'
                , $input['order_no']
                , $order->total
                , $activity->id . ' ' . $activity->title
                , $activity->id . ' ' . $activity->title
                , request()->getClientIp()
                , ''
                , request('extra')
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

        if ($channel == 'alipay_wap') {
            return route('ali.pay.charge', compact('channel', 'type', 'order_no', 'balance'));
        }

        if ($channel == 'wx_pub') {
            return route('wechat.pay.getCode', compact('channel', 'type', 'order_no', 'balance'));
        }
    }

    /**
     * 这是一个舍弃的方法,请勿使用
     * @return \Dingo\Api\Http\Response
     */
    public function miniCreateCharge()
    {
        $user = request()->user();
        $input = request()->except('_token');
        $validator = Validator::make($input, [
            'order_no' => 'required',
            'channel' => 'required',
            'openid' => 'required',
        ], [
            'order_no.required' => '提交支付请求失败,必填参数缺失',
            'channel.required' => '提交支付请求失败,必填参数缺失',
            'openid.required' => '提交支付请求失败,必填参数缺失',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return $this->api([], false, 500, $errors);
        }

        $order_no = $input['order_no'];
        $order = $this->member->with('payment')->findWhere(['order_no' => $input['order_no']])->first();
        if (!$input['order_no'] || !$order) {

            return $this->api([], false, 500, '订单不存在');
        }

        $activity = $this->activity->find($order->activity_id);
        if (!$activity) {
            return $this->api(null, false, 500, '活动不存在');
        }

        if ($order['pay_status'] == 1) {
            return $this->api([], false, 500, '订单已支付');
        }

        if ($order->status == Member::STATUS_INVALID) {
            return $this->api(null, false, 500, '无法支付');
        }

        if (request('channel') == 'wx_lite') {

            $name = $this->charge->getName();

            $charge = $this->charge->createCharge($order->user_id
                , request('channel')
                , 'activity'
                , $order_no
                , $order->price
                , $activity->id . ' ' . $activity->title
                , $activity->id . ' ' . $activity->title
                , request()->getClientIp()
                , $input['openid']
                , request('extra'));

            return $this->api(compact('charge', 'name'));
        }

        return $this->api(null, false, 500, '请求支付失败，请稍后重试');
    }

    public function paidSuccess()
    {
        $order_no = request('order_no');

        $order = $this->member->with('activity')->findWhere(['order_no' => $order_no])->first();
        if (!$order_no || !$order) {
            return $this->api([], false, 500, '订单不存在');
        }

        if ($order->pay_status != 1) {

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
            if (count($result) > 0 AND $result['metadata']['type'] == 'activity') {
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
     * 小程序支付同步通知
     * @return \Dingo\Api\Http\Response
     */
    public function miniPaidSuccess()
    {
        $order_no = request('order_no');

        $order = $this->member->with('activity')->findWhere(['order_no' => $order_no])->first();
        if (!$order_no) {
            return $this->api([], false, 500, '订单不存在');
        }

        if ($order->pay_status != 1) {
            $result = $this->charge->queryByOutTradeNumber($order_no);
            if (count($result) > 0 AND $result['metadata']['type'] == 'activity') {
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
     * 不需要现金支付的报名成功回调
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
