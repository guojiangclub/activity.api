<?php

namespace GuojiangClub\Activity\Core\Notifications;

use GuojiangClub\Activity\Core\Notifications\Channels\Wx;
use Illuminate\Bus\Queueable;

class Rewards extends Notification
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
		$user_name = '';
		if ($user->nick_name) {
			$user_name = $user->nick_name;
		} else {
			if ($user->name) {
				$user_name = $user->name;
			}
		}

		$template = [
			'first'    => '您好，您活动奖励积分已经发送到您的账户，请查收。',
			'keyword1' => $user_name,
			'keyword2' => $this->data['point']->created_at,
			'keyword3' => $this->data['point']->note,
			'keyword4' => $this->data['point']->value,
			'keyword5' => $this->data['point_total'],
			'remark'   => '点击查看积分明细',
		];

		$url = app('system_setting')->getSetting('mobile_domain');

		$template_id = app('system_setting')->getSetting('activity_point_notice');

		$data = [
			'touser'      => $this->getOpenId($user),
			'template_id' => $template_id,
			'url'         => $url . 'user/point/list?status=1',
			'data'        => $template,
		];

		return $data;
	}

}
