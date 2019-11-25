<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Discount\Checkers;

use iBrand\Component\Discount\Contracts\DiscountContract;
use iBrand\Component\Discount\Contracts\DiscountSubjectContract;
use iBrand\Component\Discount\Contracts\RuleCheckerContract;

class ContainsActivityRuleChecker implements RuleCheckerContract
{
    const TYPE = 'contains_activity';

    public function isEligible(DiscountSubjectContract $subject, array $configuration, DiscountContract $discount)
    {
        if ((is_array($configuration['items']) and in_array($subject->activity_id, $configuration['items'])) or 'all' == $configuration['items']) {
            return true;
        }

        return false;
    }
}
