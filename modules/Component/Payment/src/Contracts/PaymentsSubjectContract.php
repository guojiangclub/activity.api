<?php
namespace GuoJiangClub\Component\Payment\Contracts;

use GuoJiangClub\Component\Payment\Models\Payment;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/7
 * Time: 16:40
 */
interface PaymentsSubjectContract
{
    /**
     * add payment item
     * @param Payment $payment
     * @return mixed
     */
    public function addPayment(Payment $payment);

    /**
     * get payment subject
     * @return mixed
     */
    public function getSubject();
}