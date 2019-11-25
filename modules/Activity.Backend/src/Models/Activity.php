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

class Activity extends \GuoJiangClub\Activity\Core\Models\Activity
{
    protected $appends = ['type_text'];

    public function getTypeTextAttribute()
    {
        switch ($this->type) {
            case 'TRAIN':
                return '训练';
            break;

            case 'MATCH':
                return '赛事';
            break;

            default:
                return '旅行';
        }
    }
}
