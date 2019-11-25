<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Discount\Actions;

use GuoJiangClub\Activity\Core\Models\Adjustment;
use iBrand\Component\Discount\Contracts\DiscountActionContract;
use iBrand\Component\Discount\Contracts\DiscountContract;
use iBrand\Component\Discount\Contracts\DiscountSubjectContract;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-22
 * Time: 13:04.
 */
class ActivityAction implements DiscountActionContract
{
    const TYPE = 'activity_discount';

    public function execute(DiscountSubjectContract $subject, array $configuration, DiscountContract $discount)
    {
        $adjustment = $this->createAdjustment($discount);

        $adjustment->member_id = $subject->id;
        $adjustment->save();
    }

    public function calculate(DiscountSubjectContract $subject, array $configuration, DiscountContract $discount)
    {
        // TODO: Implement calculate() method.
    }

    private function createAdjustment(DiscountContract $discount)
    {
        if ($discount->isCouponBased()) {
            $adjustment = new Adjustment(['type' => Adjustment::ACTIVITY_DISCOUNT_ADJUSTMENT, 'label' => $discount->label, 'origin_type' => 'coupon', 'origin_id' => $discount->id]);

            return $adjustment;
        }

        $adjustment = new Adjustment(['type' => Adjustment::ACTIVITY_DISCOUNT_ADJUSTMENT, 'label' => $discount->label, 'origin_type' => 'discount', 'origin_id' => $discount->id]);

        return $adjustment;
    }

    public function combinationCalculate(DiscountSubjectContract $subject, array $configuration, DiscountContract $discount)
    {
        // TODO: Implement combinationCalculate() method.
    }
}
