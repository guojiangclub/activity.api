<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-10-29
 * Time: 17:38
 */

namespace GuojiangClub\Activity\Server\Http\Controllers;


use ElementVip\Activity\Core\Models\Member;
use Validator;
use ElementVip\Component\Payment\Contracts\PaymentChargeContract;
use ElementVip\Component\Point\Repository\PointRepository;
use ElementVip\Activity\Core\Repository\MemberRepository;
use ElementVip\Activity\Core\Repository\ActivityRepository;
use ElementVip\Component\Payment\Services\ActivityPaymentService;
use Illuminate\Events\Dispatcher;
use EasyWeChat;

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
        ActivityRepository $activityRepository
        , PaymentChargeContract $chargeContract)
    {
        $this->payment = $paymentService;
        $this->pointRepository = $pointRepository;
        $this->events = $events;
        $this->member = $memberRepository;
        $this->activity = $activityRepository;
        $this->charge = $chargeContract;
    }

    /**
     * 注释：2018-11-21 by eddy
     * @return \Dingo\Api\Http\Response|mixed
     */
//    public function createCharge()
//    {
//        $user      = request()->user();
//        $input     = request()->except('_token');
//        $validator = Validator::make($input, [
//            'order_no' => 'required',
//            'channel'  => 'required',
//            'openid'   => 'required',
//        ], [
//            'order_no.required' => '提交支付请求失败,必填参数缺失',
//            'channel.required'  => '提交支付请求失败,必填参数缺失',
//            'openid.required'   => '提交支付请求失败,必填参数缺失',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->errors()->all();
//
//            return $this->failed($errors);
//        }
//
//        $order_no = $input['order_no'];
//        $order    = $this->member->with('payment')->findWhere(['order_no' => $input['order_no']])->first();
//        if (!$input['order_no'] || count($order) <= 0) {
//
//            return $this->failed('订单不存在');
//        }
//
//        $activity = $this->activity->find($order->activity_id);
//        if (!$activity) {
//            return $this->failed( '活动不存在');
//        }
//
//        if ($order['pay_status'] == 1) {
//            return $this->failed('订单已支付');
//        }
//
//        if ($order->status == Member::STATUS_INVALID) {
//            return $this->failed( '无法支付');
//        }
//
//        if (request('channel') == 'wx_lite') {
//
//            $name = $this->charge->getName();
//
//            /*$charge = $this->charge->createCharge($order->user_id
//                , request('channel')
//                , 'activity'
//                , $order_no
//                , $order->price
//                , $activity->id . ' ' . $activity->title
//                , $activity->id . ' ' . $activity->title
//                , request()->getClientIp()
//                , $input['openid']
//                , request('extra'));*/
//
//            $payment = EasyWeChat::payment('activity');
//
//            $data = $payment->order->unify([
//                'body' => $activity->id . ' ' . $activity->title,
//                'out_trade_no' => $order_no,
//                'total_fee' => $order->price,
//                'trade_type' => 'JSAPI',
//                'openid' => \request('openid'),
//                'attach'=>json_encode(['channel'=>request('channel'),'type'=>'activity','order_sn'=>$order_no]),
//                'notify_url' => url('api/activity/wechat/notify', '', true),
//            ]);
//
//            if('FAIL' == $data['return_code']){
//                return $this->failed($data['return_msg']);
//            }
//
//            //包装成前端直接可以用的参数
//            if ('SUCCESS' == $data['return_code'] && 'SUCCESS' == $data['result_code']) {
//                $jssdk = $payment->jssdk;
//                $charge = $jssdk->sdkConfig($data['prepay_id']); // 返回数组
//                $charge['timeStamp'] = $charge['timestamp'];
//                return $this->success(compact('charge', 'name'));
//            } elseif ('FAIL' == $data['result_code']) {
//                return $this->failed($data['err_code_des']);
//            }
//            return $this->failed('支付未知错误');
//        }
//
//        return $this->failed( '请求支付失败，请稍后重试');
//    }


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

        if ($order['pay_status'] == 1) {
            return $this->failed('订单已支付');
        }

        if ($order->status == Member::STATUS_INVALID) {
            return $this->failed('无法支付');
        }

        $point = $this->pointRepository->getSumPointValid($user->id, 'default');
        if ($order->point > $point) {
            return $this->api([], false, 500, '积分不够.');
        }

        if (request('channel') == 'wx_lite') {

            $name = $this->charge->getName();

            $charge = $this->charge->createCharge($order->user_id
                , request('channel')
                , 'activity'
                , $order_no
                , $order->total
                , $activity->id . ' ' . $activity->title
                , $activity->id . ' ' . $activity->title
                , request()->getClientIp()
                , $input['openid']
                , request('extra'));

            return $this->api(compact('charge'));
        }

        return $this->failed('请求支付失败，请稍后重试');
    }
}