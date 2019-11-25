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

use iBrand\Component\Discount\Models\Rule as BaseRule;

class Rule extends BaseRule
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $table = 'ac_discount_rule';
}
