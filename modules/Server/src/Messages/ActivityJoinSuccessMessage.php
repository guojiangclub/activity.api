<?php

namespace GuoJiangClub\Activity\Server\Messages;

use Overtrue\EasySms\Message;
use Overtrue\EasySms\Contracts\GatewayInterface;

class ActivityJoinSuccessMessage extends Message
{
	protected $activity;

	public function __construct($activity)
	{
		$this->activity = $activity;
	}

	public function getContent(GatewayInterface $gateway = null)
	{
		return sprintf('您已成功报名%s活动，请于%s%s领取参赛包。', $this->activity->title, date('Y-m-d H:i', strtotime($this->activity->package_get_time)), $this->activity->package_get_address);
	}

	public function getTemplate(GatewayInterface $gateway = null)
	{
		return app('system_setting')->getSetting('activity_sms_template_id');
	}

	public function getData(GatewayInterface $gateway = null)
	{
		return [
			'name'    => $this->activity->title,
			'time'    => date('Y-m-d H:i', strtotime($this->activity->package_get_time)),
			'address' => $this->activity->package_get_address,
		];
	}
}