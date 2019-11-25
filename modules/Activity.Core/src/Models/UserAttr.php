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

class UserAttr extends Model
{
    protected $table = 'ac_user_attr';

    protected $fillable = ['key', 'value'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
