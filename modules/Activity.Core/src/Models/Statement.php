<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    protected $table = 'ac_activity_statement';
    protected $guarded = ['id'];
}
