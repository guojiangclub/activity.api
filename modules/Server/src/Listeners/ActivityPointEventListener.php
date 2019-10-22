<?php

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
		$note  = '';
		if ($type == self::ACTIVITY_JOIN && $join = $activity->points()->where('type', self::ACTIVITY_JOIN)->first()) {
			$point = $join->value;
			$note  = '活动报名奖励积分';
		}

		if ($type == self::ACTIVITY_SIGN && $sign = $activity->points()->where('type', self::ACTIVITY_SIGN)->first()) {
			$point = $sign->value;
			$note  = '活动签到奖励积分';
		}

		if ($point > 0) {
			//积分报名
			$this->pointRepository->create([
				'user_id' => $user_id,
				'type'    => 'default',
				'action'  => 'activity',
				'note'    => $note,
				'value'   => $point,
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
