<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-03-21
 * Time: 1:00
 */

namespace GuoJiangClub\Component\Payment\Contracts;


interface PaymentChargeContract
{
    public function getName();

    public function createCharge($user_id, $channel, $type = 'order', $order_no, $amount, $subject, $body, $ip = '127.0.0.1', $openid = '', $extra = [], $submit_time = '');
  
    public function createPaymentLog($action, $operate_time, $order_no, $transcation_order_no, $transcation_no, $amount, $channel, $type = 'order', $status, $user_id, $meta = []);

    public function queryByOutTradeNumber($order_no);
}