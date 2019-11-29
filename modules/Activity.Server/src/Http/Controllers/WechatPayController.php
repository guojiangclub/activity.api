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
use GuoJiangClub\Component\Payment\Contracts\PaymentChargeContract;
use GuoJiangClub\Component\Payment\Services\ActivityPaymentService;
use iBrand\Component\Point\Repository\PointRepository;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use Illuminate\Events\Dispatcher;
use Validator;

class WechatPayController extends Controller
{
    private $payment;
    private $pointRepository;
    private $events;
    private $member;
    private $activity;
    private $charge;

    public function __construct(
        ActivityPaymentService $paymentService,
        PointRepository $pointRepository,
        Dispatcher $events,
        MemberRepository $memberRepository,
        ActivityRepository $activityRepository, PaymentChargeContract $chargeContract)
    {
        $this->payment = $paymentService;
        $this->pointRepository = $pointRepository;
        $this->events = $events;
        $this->member = $memberRepository;
        $this->activity = $activityRepository;
        $this->charge = $chargeContract;
    }


    public function createCharge()
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

            return $this->failed($errors);
        }

        $order_no = $input['order_no'];
        $order = $this->member->with('payment')->findWhere(['order_no' => $input['order_no']])->first();
        if (!$input['order_no'] || !$order) {
            return $this->failed('订单不存在');
        }

        $activity = $this->activity->find($order->activity_id);
        if (!$activity) {
            return $this->failed('活动不存在');
        }

        if (1 == $order['pay_status']) {
            return $this->failed('订单已支付');
        }

        if (Member::STATUS_INVALID == $order->status) {
            return $this->failed('无法支付');
        }

        $point = $this->pointRepository->getSumPointValid($user->id);
        if ($order->point > $point) {
            return $this->api([], false, 500, '积分不够.');
        }

        if ('wx_lite' == request('channel')) {
            $name = $this->charge->getName();

            $charge = $this->charge->createCharge($order->user_id, request('channel'), 'activity', $order_no, $order->total, $activity->id.' '.$activity->title, $activity->id.' '.$activity->title, request()->getClientIp(), $input['openid'], request('extra'));

            return $this->api(compact('charge'));
        }

        return $this->failed('请求支付失败，请稍后重试');
    }
}
