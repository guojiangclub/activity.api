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

use iBrand\Component\Discount\Models\Discount as BaseDiscount;

class Discount extends BaseDiscount
{
    protected $table = 'ac_discount';

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}
