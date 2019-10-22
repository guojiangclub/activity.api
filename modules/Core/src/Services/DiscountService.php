<?php

namespace GuojiangClub\Activity\Core\Services;

use GuojiangClub\Activity\Core\Repository\CouponRepository;
use GuojiangClub\Activity\Core\Repository\DiscountRepository;
use ElementVip\Component\Discount\Applicators\DiscountApplicator;
use ElementVip\Component\Discount\Checkers\CouponEligibilityChecker;
use ElementVip\Component\Discount\Checkers\DatesEligibilityChecker;
use ElementVip\Component\Discount\Checkers\DiscountEligibilityChecker;

class DiscountService extends \ElementVip\Component\Discount\Services\DiscountService {

    public function __construct(DiscountRepository $discountRepository
        , DiscountEligibilityChecker $discountEligibilityChecker
        , CouponRepository $couponRepository
        , CouponEligibilityChecker $couponEligibilityChecker
        , DiscountApplicator $discountApplicator
        , DatesEligibilityChecker $datesEligibilityChecker)
    {
        parent::__construct($discountRepository, $discountEligibilityChecker, $couponRepository, $couponEligibilityChecker, $discountApplicator, $datesEligibilityChecker);
    }

}