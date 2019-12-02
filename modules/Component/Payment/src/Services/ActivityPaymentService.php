<?php

namespace GuoJiangClub\Component\Payment\Services;

use Carbon\Carbon;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Models\PaymentDetail;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Component\Payment\Contracts\PaymentChargeContract;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use iBrand\Component\Point\Repository\PointRepository;
use GuoJiangClub\Component\Payment\Models\Payment;
use iBrand\Component\User\Models\User;

class ActivityPaymentService
{
    private $member;
    private $paymentRepository;
    private $pointRepository;
    private $pay;

    public function __construct(MemberRepository $memberRepository,
                                PaymentRepository $paymentRepository,
                                PointRepository $pointRepository,
                                PaymentChargeContract $paymentChargeContract)
    {
        $this->member = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->pointRepository = $pointRepository;
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
            }


            $activity->update(['member_count' => $activity->member_count + 1]);
            if ($payment->limit > 0 && $payment->is_limit == 1) {
                $payment->update(['limit' => $payment->limit - 1]);
            }

            event('on.member.activity.status.change', [$user->id, $activity, 'act_join']);

        }
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
