<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Models\Discount;

use iBrand\Component\Discount\Models\Coupon as BaseCoupon;

class Coupon extends BaseCoupon
{
    protected $table = 'ac_discount_coupon';
    protected $appends = ['discount_amount', 'discount_percentage', 'starts_at', 'ends_at'];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
