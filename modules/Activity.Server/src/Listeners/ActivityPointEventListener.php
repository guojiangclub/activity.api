<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Server\Listeners;

use iBrand\Component\Point\Repository\PointRepository;

class ActivityPointEventListener
{
    const ACTIVITY_JOIN = 'act_join';
    const ACTIVITY_SIGN = 'act_sign';

    protected $pointRepository;

    public function __construct(PointRepository $pointRepository)
    {
        $this->pointRepository = $pointRepository;
    }

    public function point($user_id, $activity, $type)
    {
        $point = 0;
        $note = '';
        if (self::ACTIVITY_JOIN == $type && $join = $activity->points()->where('type', self::ACTIVITY_JOIN)->first()) {
            $point = $join->value;
            $note = '活动报名奖励积分';
        }

        if (self::ACTIVITY_SIGN == $type && $sign = $activity->points()->where('type', self::ACTIVITY_SIGN)->first()) {
            $point = $sign->value;
            $note = '活动签到奖励积分';
        }

        if ($point > 0) {
            //积分报名
            $this->pointRepository->create([
                'user_id' => $user_id,
                'type' => 'default',
                'action' => 'activity',
                'note' => $note,
                'value' => $point,
            ]);
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'on.member.activity.status.change',
            'GuoJiangClub\Activity\Server\Listeners\ActivityPointEventListener@point'
        );
    }
}
