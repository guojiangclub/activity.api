<?php
namespace GuoJiangClub\Activity\Core\Discount\Checkers;

use iBrand\Component\Discount\Contracts\DiscountContract;
use iBrand\Component\Discount\Contracts\DiscountSubjectContract;
use iBrand\Component\Discount\Contracts\RuleCheckerContract;
use iBrand\Component\Discount\Contracts\DiscountItemContract;

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