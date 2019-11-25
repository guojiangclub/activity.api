<?php

/*
 * This file is part of guojiangclub/activity-backend.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Backend\Models;

use GuoJiangClub\Activity\Core\Models\Discount\Action;

class DiscountAction extends Action
{
    public function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = json_encode(['item' => $value]);
    }
}
