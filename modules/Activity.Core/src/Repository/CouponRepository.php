<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Repository;

use iBrand\Component\Discount\Contracts\DiscountContract;

interface CouponRepository extends \iBrand\Component\Discount\Repositories\CouponRepository
{
    public function canGetCoupon(DiscountContract $discount, $user);

    public function canGetCouponByMonth(DiscountContract $discount, $user);

    public function canGetCouponNumByMonth(DiscountContract $discount, $user);
}
