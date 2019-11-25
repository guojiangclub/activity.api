<?php

/*
 * This file is part of guojiangclub/activity-backend.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Backend\Models;

use ElementVip\Component\User\Models\User;
use GuoJiangClub\Activity\Core\Models\Discount\Coupon;

class DiscountCoupon extends Coupon
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
