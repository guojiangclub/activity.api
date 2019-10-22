<?php

namespace GuojiangClub\Activity\Core\Notifications;

use GuojiangClub\Activity\Core\Notifications\Channels\Wx;
use Illuminate\Bus\Queueable;

class Signed extends Notification
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
		$template = [
			'first'    => '您报名的活动，签到成功。',
			'keyword1' => $this->data['activity']->title,
			'keyword2' => $this->data['member']->signed_at,
			'keyword3' => $this->data['activity']->address . ' ' . $this->data['activity']->address_name,
			'remark'   => '详情请点击查看',
		];

		$url = app('system_setting')->getSetting('mobile_activity_domain');

		$template_id = app('system_setting')->getSetting('activity_sign_notice');

		$data = [
			'touser'      => $this->getOpenId($user),
			'template_id' => $template_id,
			'url'         => $url . 'registration/activity_detail/' . $this->data['activity']->id,
			'data'        => $template,
		];

		return $data;
	}

}
