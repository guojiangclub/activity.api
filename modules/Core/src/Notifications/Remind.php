<?php

namespace GuoJiangClub\Activity\Core\Notifications;

use Carbon\Carbon;
use GuoJiangClub\Activity\Core\Notifications\Channels\Wx;
use Illuminate\Bus\Queueable;

class Remind extends Notification
{
	use Queueable;

	/**
	 * Get the notification's delivery channels.
	 *
	 * @return array
	 */
	public function via()
	{
		return [Wx::class];
	}

	/**
	 * @param $notifiable
	 *
	 * @return array|bool
	 */
	public function handle($notifiable)
	{
		$activity = $this->data['activity'];

		if (empty($activity)) {
			return false;
		}
		if (empty($member = $activity->members()->where('user_id', $notifiable->id)->first())) {
			return false;
		}
		if ($member->remind_at) {
			return false;
		}  //如果已经通知过则返回false

		if ($this->checkOpenId($notifiable)) {
			$member->remind_at = Carbon::now();;
			$member->save();

			return $this->getData($notifiable);
		}

		return false;
	}

	private function getData($user)
	{
		$template = [
			'first'    => '您报名参加的野练，明天将会开始。',
			'keynote1' => $this->data['activity']->title,
			'keynote2' => $this->data['activity']->starts_at,
			'remark'   => '请您前往现场准时参加！活动详情请点击查看。',
		];

		$url = app('system_setting')->getSetting('mobile_activity_domain');

		$template_id = app('system_setting')->getSetting('activity_remind_notice');

		$data = [
			'touser'      => $this->getOpenId($user),
			'template_id' => $template_id,
			'url'         => $url . 'registration/activity_detail/' . $this->data['activity']->id,
			'data'        => $template,
		];

		return $data;
	}

}
