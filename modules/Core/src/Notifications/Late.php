<?php

namespace GuojiangClub\Activity\Core\Notifications;

use GuojiangClub\Activity\Core\Notifications\Channels\Wx;
use Illuminate\Bus\Queueable;

class Late extends Notification
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
		if ($member->signed_at) {
			return false;
		}  //如果已经签到了则返回false

		if ($this->checkOpenId($notifiable)) {
			return $this->getData($notifiable);
		}

		return false;
	}

	private function getData($user)
	{
		$time = $this->getFormatTime($this->data['activity']->starts_at, $this->data['activity']->ends_at);

		$template = [
			'first'    => '您报名的活动已结束，很遗憾您未正常出席签到。',
			'keyword1' => $this->data['activity']->title,
			'keyword2' => $time,
			'keyword3' => $this->data['activity']->address . ' ' . $this->data['activity']->address_name,
			'remark'   => '详情请点击查看',
		];

		$url = app('system_setting')->getSetting('mobile_activity_domain');

		$template_id = app('system_setting')->getSetting('activity_end_notice');

		$data = [
			'touser'      => $this->getOpenId($user),
			'template_id' => $template_id,
			'url'         => $url . 'registration/activity_detail/' . $this->data['activity']->id,
			'data'        => $template,
		];

		return $data;
	}

}
