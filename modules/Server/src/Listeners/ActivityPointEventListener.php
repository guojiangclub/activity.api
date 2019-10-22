<?php

namespace GuojiangClub\Activity\Server\Listeners;

use ElementVip\Component\User\Models\User;
use ElementVip\Component\Point\Repository\PointRepository;
use ElementVip\Notifications\PointRecord;
use ElementVip\Notifications\ActivityJoinSuccess;
use GuojiangClub\Activity\Server\Messages\ActivityJoinSuccessMessage;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use iBrand\Sms\Jobs\DbLogger;
use Overtrue\EasySms\EasySms;

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

		$user = User::find($user_id);
		if ($point > 0) {
			//积分报名
			$this->pointRepository->create([
				'user_id' => $user_id,
				'type'    => 'default',
				'action'  => 'activity',
				'note'    => $note,
				'value'   => $point,
			]);

			$user->notify(new PointRecord(['point' => [
				'user_id' => $user->id,
				'action'  => 'activity',
				'note'    => $note,
				'value'   => $point,
			]]));
		}

		if ($type == self::ACTIVITY_JOIN && 1 == $activity->send_message) {
			if ($user->mobile) {
				try {
					$config  = config('ibrand.sms.easy_sms');
					$easySms = new EasySms($config);
					$message = new ActivityJoinSuccessMessage($activity);
					$easySms->send($user->mobile, $message);
				} catch (NoGatewayAvailableException $noGatewayAvailableException) {
					$noGatewayAvailableException->results;
				} catch (\Exception $exception) {
					\Log::info($exception->getMessage());
				}
			}

			//模板消息
			$user->notify(new ActivityJoinSuccess(['activity' => $activity]));
		}
	}

	public function subscribe($events)
	{
		$events->listen(
			'on.member.activity.status.change',
			'GuojiangClub\Activity\Server\Listeners\ActivityPointEventListener@point'
		);
	}
}
