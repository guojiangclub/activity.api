<?php

namespace GuoJiangClub\Component\Payment\Services;

use GuoJiangClub\Component\Payment\Models\Payment;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Models\PaymentDetail;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use iBrand\Component\Point\Repository\PointRepository;
use iBrand\Component\User\Models\User;
use Route;


class PayService
{
    private $member;
    private $paymentRepository;
    private $pointRepository;

    public function __construct(
        MemberRepository $memberRepository
        , PaymentRepository $paymentRepository
        , PointRepository $pointRepository
    )
    {
        $this->member = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->pointRepository = $pointRepository;
    }

    public function paySuccess($charge)
    {
        $type = $charge['type'];
        //充值
        if ($type == 'activity') {
            $this->ActivityPaySuccess($charge);
        }
    }


    protected function ActivityPaySuccess($charge)
    {

        $order_no = $charge['out_trade_no'];
        $type = $charge['type'];
        //更改订单状态
        $order = $this->member->findWhere(['order_no' => $order_no])->first();

        if (!PaymentDetail::where('channel_no', $charge['trade_no'])->where('order_id', $order->id)->first()) {

            PaymentDetail::create([
                'order_id' => $order->id,
                'channel' => $charge['channel'],
                'amount' => $charge['total_amount'] * 100,
                'status' => PaymentDetail::STATUS_COMPLETED,
                'channel_no' => $charge['trade_no'],
                'pingxx_no' => ''
                , 'paid_at' => $charge['send_pay_date']
            ]);

            $this->member->update(['status' => 1, 'pay_status' => 1], $order->id);

            $activity = Activity::find($order->activity_id);

            $payment = $this->paymentRepository->findWhere([['id', '=', $order->payment_id], ['activity_id', '=', $order->activity_id]])->first();
            $user = User::find($order->user_id);
            if ($payment->type == 2) {
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

            if ($payment->type == 1 || $payment->type == 2) {
                $activity->update(['member_count' => $activity->member_count + 1]);
                if ($payment->limit > 0 && $payment->is_limit == 1) {
                    $payment->update(['limit' => $payment->limit - 1]);
                }
            }
        }

    }


}