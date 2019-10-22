<?php

namespace GuojiangClub\Activity\Core\Notifications;

use GuojiangClub\Activity\Core\Notifications\Channels\Wx;
use Illuminate\Bus\Queueable;

class Join extends Notification
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
		if ($this->checkOpenId($notifiable)) {
			return $this->getData($notifiable);
		}

		return false;
	}

	private function getData($user)
	{
		$time = $this->getFormatTime($this->data['activity']->starts_at, $this->data['activity']->ends_at);

		$url = app('system_setting')->getSetting('mobile_activity_domain');

		$template_id = app('system_setting')->getSetting('activity_join_notice');

		$user_name = '';
		if ($user->nick_name) {
			$user_name = $user->nick_name;
		} else {
			if ($user->name) {
				$user_name = $user->name;
			}
		}

		$template = [
			'first'    => '您好，恭喜您成功报名活动。',
			'keyword1' => $this->data['activity']->title,
			'keyword2' => $time,
			'keyword3' => $this->data['activity']->address . ' ' . $this->data['activity']->address_name,
			'keyword4' => $user_name,
			'remark'   => '详情请点击查看',
		];

		$data = [
			'touser'      => $this->getOpenId($user),
			'template_id' => $template_id,
			'url'         => $url . 'registration/activity_detail/' . $this->data['activity']->id,
			'data'        => $template,
		];

		return $data;
	}
}
