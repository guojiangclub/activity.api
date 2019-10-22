<?php
namespace GuojiangClub\Activity\Core\Discount\Checkers;

use ElementVip\Component\Discount\Contracts\DiscountContract;
use ElementVip\Component\Discount\Contracts\DiscountSubjectContract;
use ElementVip\Component\Discount\Contracts\RuleCheckerContract;
use ElementVip\Component\Discount\Contracts\DiscountItemContract;

class ContainsActivityRuleChecker implements RuleCheckerContract
{
    const TYPE = 'contains_activity';


    public function isEligible(DiscountSubjectContract $subject, array $configuration,DiscountContract $discount)
    {
        if((is_array($configuration['items']) AND in_array($subject->activity_id,$configuration['items'])) OR $configuration['items']=='all'){
            return true;
        }
        return false;
    }
}