<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Repository\Eloquent;

use iBrand\Component\Discount\Contracts\DiscountContract;
use GuoJiangClub\Activity\Core\Models\Discount\Coupon;
use GuoJiangClub\Activity\Core\Repository\CouponRepository;
//use

class CouponRepositoryEloquent extends \iBrand\Component\Discount\Repositories\Eloquent\CouponRepositoryEloquent implements CouponRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Coupon::class;
    }

    public function canGetCoupon(DiscountContract $discount, $user)
    {
        if ($discount->usage_limit <= $discount->used) {
            return false;
        }
        $coupons = Coupon::where('discount_id', $discount->id)->where('user_id', $user->id)->count();
        if ($discount->per_usage_limit <= $coupons) {
            return false;
        }

        return true;
    }

    /**
     * 每月领取2张PASS判断.
     *
     * @param DiscountContract $discount
     * @param $user
     *
     * @return bool
     */
    public function canGetCouponByMonth(DiscountContract $discount, $user)
    {
        if (Coupon::where('discount_id', $discount->id)->where('user_id', $user->id)->where('created_at', '>', date('Y-m-01 0:00:00'))->count() >= 2) {
            return false;
        }

        return true;
    }

    /**
     * 用户每月可领取PASS数量判断.
     *
     * @param DiscountContract $discount
     * @param $user
     *
     * @return int
     */
    public function canGetCouponNumByMonth(DiscountContract $discount, $user)
    {
        $count = Coupon::where('discount_id', $discount->id)->where('user_id', $user->id)->where('created_at', '>', date('Y-m-01 0:00:00'))->count();
        switch ($count) {
            case 0:
                return 2;
                break;
            case 1:
                return 1;
                break;
            default:
                return 0;
        }
    }
}
