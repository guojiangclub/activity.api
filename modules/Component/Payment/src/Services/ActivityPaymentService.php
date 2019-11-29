<?php

namespace GuoJiangClub\Component\Payment\Services;

use Carbon\Carbon;
use GuoJiangClub\Activity\Core\Models\ActivityOrders;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Models\PaymentDetail;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Payment\Contracts\PaymentChargeContract;
use GuoJiangClub\Notifications\PointRecord;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use iBrand\Component\Point\Repository\PointRepository;
use GuoJiangClub\Component\Payment\Models\Payment;
use Pingpp\Charge;
use Pingpp\Pingpp;

class ActivityPaymentService
{
    private $member;
    private $paymentRepository;
    private $pointRepository;
    private $paymentService;
    private $pay;

    public function __construct(MemberRepository $memberRepository,
                                PaymentRepository $paymentRepository,
                                PointRepository $pointRepository,
                                PaymentService $paymentService,
                                PaymentChargeContract $paymentChargeContract)
    {
        $this->member = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->pointRepository = $pointRepository;
        $this->paymentService = $paymentService;
        $this->pay = $paymentChargeContract;
    }

    /**
     * 支付成功操作，只有pingpp webhooks通知时才能更改订单状态
     *
     * @param array $charge
     */
    public function paySuccess(array $charge)
    {
        $order_no = $charge['metadata']['order_sn'];
        //更改订单状态
        $order = $this->member->findWhere(['order_no' => $order_no])->first();
        if (!PaymentDetail::where('pingxx_no', $charge['id'])->where('order_id', $order->id)->first()) {
            PaymentDetail::create([
                'order_id' => $order->id,
                'channel' => $charge['channel'],
                /*'amount' => $charge['amount'],*/
                'amount' => $order->price,
                'status' => PaymentDetail::STATUS_COMPLETED,
                'channel_no' => $charge['transaction_no'],
                'pingxx_no' => $charge['id'],
                'paid_at' => Carbon::createFromTimestamp($charge['time_paid']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->member->update(['status' => 1, 'pay_status' => 1], $order->id);

            $activity = Activity::find($order->activity_id);
            $payment = $this->paymentRepository->findWhere([['id', '=', $order->payment_id], ['activity_id', '=', $order->activity_id]])->first();
            $user = User::find($order->user_id);
            /*if ($payment->type == 2) {*/
            if ($payment->point > 0) {  //如果使用了积分
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
                    'value' => $payment->point,
                    'valid_time' => 0,
                    'item_type' => Payment::class,
                    'item_id' => $payment->id,
                ]]));
            }

            /*if ($payment->type == 1 || $payment->type == 2) {*/
            $activity->update(['member_count' => $activity->member_count + 1]);
            if ($payment->limit > 0 && $payment->is_limit == 1) {
                $payment->update(['limit' => $payment->limit - 1]);
            }
            /*}*/

            event('on.member.activity.status.change', [$user->id, $activity, 'act_join']);

            if ($orderRelation = ActivityOrders::where('member_id', $order->id)->get()->last()) {
                $shopOrder = Order::find($orderRelation->order_id);               
                $this->pay->createPaymentLog('result_pay', Carbon::createFromTimestamp($charge['time_paid']), $shopOrder->order_no, $charge['id'], $charge['transaction_no'], $charge['amount'], $charge['channel'], 'order', 'SUCCESS', $user->id, $charge);
                
                $shopOrder->type = Order::TYPE_ACTIVITY;
                $shopOrder->save();
                
                $charge['metadata']['order_sn'] = $shopOrder->order_no;
                $charge['metadata']['type'] = 'order';
                $charge['amount'] = $shopOrder->total;
                $this->paymentService->paySuccess($charge);
            }
        }
    }

    /**
     * 创建charge数据，与pingpp集成对接
     *
     * @param        $user_id
     * @param        $channel
     * @param string $type
     * @param        $order_no
     * @param        $amount
     * @param        $subject
     * @param        $body
     * @param string $ip
     *
     * @return Charge
     */
    public function createCharge($user_id, $channel, $type = 'order', $order_no, $amount, $subject, $body, $ip = '127.0.0.1', $openid = '', $extra = [])
    {
        Pingpp::setApiKey($this->getApiKey());
        $this->setPrivateKey();

        $extra = $this->createExtra($channel, $openid, $extra);

        $delayTime = app('system_setting')->getSetting('order_auto_cancel_time') ? app('system_setting')->getSetting('order_auto_cancel_time') : 1440;

        if (in_array($channel, ['wx_pub', 'wx_pub_qr'])) {
            $delayTime = 120;
        }

        $chargeData = [
            'app' => ['id' => $this->getPingAppId()],
            'channel' => $channel,
            'currency' => 'cny',
            'amount' => $amount, //因为pingpp 是以分为单位
            'client_ip' => $ip,
            'order_no' => $this->getWxPayCode($order_no, $channel),
            'subject' => mb_strcut($subject, 0, 32, 'UTF-8'),
            'body' => mb_strcut($body, 0, 32, 'UTF-8'),
            'extra' => $extra,
            'metadata' => ['user_id' => $user_id, 'order_sn' => $order_no, 'charge_type' => 'activity_pay'],
            'time_expire' => Carbon::now()->addMinute($delayTime)->timestamp,
        ];

        $charge = Charge::create($chargeData);

        return $charge;
    }

    /**
     * 根据充值渠道生成extra参数
     *
     * @param $channel
     *
     * @return array
     */
    private function createExtra($channel, $openid = '', $extra = [])
    {
        $result = [];
        switch ($channel) {
            case 'alipay_wap':
                $result = [
                    'success_url' => isset($extra['success_url']) ? $extra['success_url'] : config('payment.channel.alipay_wap.success_url'),
                    'cancel_url' => isset($extra['cancel_url']) ? $extra['cancel_url'] : config('payment.channel.alipay_wap.cancel_url'),
                ];
                break;
            case 'alipay_pc_direct':
                $result = [
                    'success_url' => isset($extra['success_url']) ? $extra['success_url'] : config('payment.channel.alipay_pc_direct.success_url'),
                ];
                break;
            case 'upacp_wap':
                $result = [
                    'result_url' => isset($extra['success_url']) ? $extra['success_url'] : config('payment.channel.upacp_wap.result_url'),
                ];
                break;
            case 'wx_pub':
                $result = [
                    'open_id' => $openid,
                ];
                break;
            case 'wx_pub_qr':
                $result = [
                    'product_id' => isset($extra['product_id']) ? $extra['product_id'] : "tempProductId",
                ];
                break;

            case 'jdpay_wap':
                $result = [
                    'success_url' => isset($extra['success_url']) ? $extra['success_url'] : config('payment.channel.jdpay_wap.success_url'),
                    'fail_url' => isset($extra['cancel_url']) ? $extra['cancel_url'] : config('payment.channel.jdpay_wap.fail_url'),
                ];
                break;
        }

        return $result;
    }

    private function getWxPayCode($order_sn, $channel)
    {
        switch ($channel) {
            case 'wx_pub':
            case 'wx_pub_qr':
                return build_order_no('WXNO');
            default:
                return $order_sn;
        }
    }

    private function getPingAppId()
    {
        if ($appId = settings('pingxx_app_id')) {
            return $appId;
        }

        return config('payment.pingxx_app_id');
    }

    private function getApiKey()
    {
        if (settings('pingxx_pay_scene') AND settings('pingxx_pay_scene') == 'live' AND $apiKey = settings('pingxx_live_secret_key')) {
            return $apiKey;
        }

        if ($apiKey = settings('pingxx_test_secret_key')) {
            return $apiKey;
        }

        return config('payment.pingxx_live_secret_key');
    }

    private function setPrivateKey()
    {
        Pingpp::setPrivateKeyPath(storage_path('share') . '/rsa_private_key.pem');
    }
}
