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
use ElementVip\Component\Payment\Services\ActivityPaymentService;

class WechatPayNotifyController extends Controller
{
    protected $activityPayment;

    public function __construct(ActivityPaymentService $activityPaymentService)
    {
        $this->activityPayment = $activityPaymentService;
    }

    protected function notify()
    {
        $payment = EasyWeChat::payment('activity');

        $response = $payment->handlePaidNotify(function ($message, $fail) {
            if ('SUCCESS' === $message['return_code']) { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ('SUCCESS' === array_get($message, 'result_code')) {
                    $attach = json_decode($message['attach'], true);

                    $charge['metadata']['order_sn'] = $attach['order_sn'];
                    $charge['metadata']['type'] = $attach['type'];
                    $charge['amount'] = $message['total_fee'];
                    $charge['transaction_no'] = $message['transaction_id'];
                    $charge['channel'] = $attach['channel'];
                    $charge['id'] = $message['out_trade_no'];
                    $charge['time_paid'] = strtotime($message['time_end']);
                    $charge['details'] = json_encode($message);

                    $this->activityPayment->paySuccess($charge);

                    return true; // 返回处理完成

                    // 用户支付失败
                } elseif ('FAIL' === array_get($message, 'result_code')) {
                    return $fail('支付失败');
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            return $fail('支付失败');
        });

        return $response;
    }
}
