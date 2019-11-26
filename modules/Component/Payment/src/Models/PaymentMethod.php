<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/7
 * Time: 16:36
 */

namespace GuoJiangClub\Component\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'el_payment_method';

    protected $guarded = ['id'];
}