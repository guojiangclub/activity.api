<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/16
 * Time: 18:00
 */

namespace GuoJiangClub\Component\Payment\Models;


use Illuminate\Database\Eloquent\Model;

class PaymentRefundLog extends Model
{
    protected $table = 'el_payment_refund_log';

    protected $guarded = ['id'];
}