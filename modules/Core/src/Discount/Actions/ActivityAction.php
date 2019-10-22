<?php

namespace GuojiangClub\Activity\Core\Discount\Actions;

use GuojiangClub\Activity\Core\Models\Adjustment;
use ElementVip\Component\Discount\Contracts\DiscountActionContract;
use ElementVip\Component\Discount\Contracts\DiscountContract;
use ElementVip\Component\Discount\Contracts\DiscountSubjectContract;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-22
 * Time: 13:04
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
            $adjustment = new Adjustment(['type' => Adjustment::ACTIVITY_DISCOUNT_ADJUSTMENT
                , 'label' => $discount->label, 'origin_type' => 'coupon', 'origin_id' => $discount->id]);
            return $adjustment;
        }

        $adjustment = new Adjustment(['type' => Adjustment::ACTIVITY_DISCOUNT_ADJUSTMENT
            , 'label' => $discount->label, 'origin_type' => 'discount', 'origin_id' => $discount->id]);
        return $adjustment;
    }

    public function combinationCalculate(DiscountSubjectContract $subject, array $configuration, DiscountContract $discount)
    {
        // TODO: Implement combinationCalculate() method.
    }
}