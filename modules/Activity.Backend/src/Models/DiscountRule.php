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

use GuoJiangClub\Activity\Core\Models\Discount\Rule;

class DiscountRule extends Rule
{
    public function setConfigurationAttribute($value)
    {
        $type = $this->attributes['type'];

        if ('contains_activity' == $type) {
            if ('all' == $value) {
                $data['items'] = 'all';
            } else {
                $data['items'] = explode(',', $value);
            }

            $this->attributes['configuration'] = json_encode($data);
        }
    }

    public function getConfigurationAttribute()
    {
        $type = $this->attributes['type'];
        $value = json_decode($this->attributes['configuration'], true);

        if ('contains_activity' == $type) {
            if ('all' == $value['items']) {
                return 'all';
            }

            return implode(',', $value['items']);
        }
    }
}
