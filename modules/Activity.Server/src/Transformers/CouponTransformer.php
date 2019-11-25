<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Server\Transformers;

class CouponTransformer extends BaseTransformer
{
    public static $excludeable = [
        'deleted_at',
    ];

    public function transformData($model)
    {
        $res = array_except($model->toArray(), self::$excludeable);

        return $res;
    }
}
