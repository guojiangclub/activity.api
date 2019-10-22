<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/1
 * Time: 13:30
 */

namespace GuojiangClub\Activity\Admin\Models;


use ElementVip\Activity\Core\Models\Discount\Coupon;
use ElementVip\Component\User\Models\User;

class DiscountCoupon extends Coupon
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}