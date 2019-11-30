<?php

namespace GuoJiangClub\Component\Payment\Services;

use Carbon\Carbon;
use GuoJiangClub\Component\Balance\Model\Balance;
use GuoJiangClub\Component\Balance\Model\BalanceOrder;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Component\User\Models\UserBind;
use GuoJiangClub\Component\Payment\Models\Payment;
use GuoJiangClub\Notifications\ChargeSuccess;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Models\PaymentDetail;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use iBrand\Component\Point\Repository\PointRepository;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use Route;


class PayService
{
    private $orderRepository;
    private $orderProcessor;

    private $member;
    private $paymentRepository;
    private $pointRepository;

    public function __construct(
        , MemberRepository $memberRepository
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
        if ($type == 'recharge') {
            $this->RechargePaySuccess($charge);
            //订单
        } else if ($type == 'order') {
            $this->OrderPaySuccess($charge);
            //活动
        } else if ($type == 'activity') {
            $this->ActivityPaySuccess($charge);
        }

    }

    protected function RechargePaySuccess($charge)
    {

        $order_no = $charge['out_trade_no'];

        $type = $charge['type'];

        $order = BalanceOrder::where('order_no', $order_no)->first();

        if ($order AND $order->pay_status == 0 AND $order->pay_amount == $charge['total_amount'] * 100) {

            $order->pay_status = 1;
            $order->pay_time = Carbon::now();
            $order->save();

            $balance = Balance::create(['user_id' => $order->user_id, 'type' => 'recharge', 'note' => '充值', 'value' => $order->amount, 'origin_id' => $order->id, 'origin_type' => BalanceOrder::class]);

            event('recharge.success', [$order]);
            $user = User::find($order->user_id);
            $user->notify(new ChargeSuccess(['charge' => ['user_id' => $order->user_id, 'type' => 'recharge', 'note' => '充值', 'value' => $order->amount, 'origin_id' => $order->id, 'origin_type' => BalanceOrder::class]]));
        }
    }

    protected function OrderPaySuccess($charge)
    {
        \Log::info($charge);
        $order_no = $charge['out_trade_no'];
        $type = $charge['type'];
        //更改订单状态
        $order = $this->orderRepository->getOrderByNo($order_no);

        $need_pay = $order->getNeedPayAmount();
        $pay_state = $charge['total_amount'] * 100 - $need_pay;

        $order_pay = Payment::where('channel_no', $charge['trade_no'])->where('order_id', $order->id)->first();
        if ($order_pay And $order_pay->channel != 'balance') {
            return;
        }

        if ($pay_state >= 0) {
            $order = $this->orderRepository->getOrderByNo($order_no);

            $payment = new Payment([
                'order_id' => $order->id,
                'channel' => $charge['channel'],
                'amount' => $charge['total_amount'] * 100,
                'status' => Payment::STATUS_COMPLETED
                , 'channel_no' => $charge['trade_no'],
                'pingxx_no' => ''
                , 'paid_at' => $charge['send_pay_date']
            ]);

            $order->payments()->save($payment);

            event('order.customer.paid', [$order]);

            $this->orderProcessor->process($order);

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

            if ($payment->type == 1 || $payment->type == 2) {
                $activity->update(['member_count' => $activity->member_count + 1]);
                if ($payment->limit > 0 && $payment->is_limit == 1) {
                    $payment->update(['limit' => $payment->limit - 1]);
                }
            }
        }

    }


}