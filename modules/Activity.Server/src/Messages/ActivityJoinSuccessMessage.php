<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Server\Messages;

use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Message;

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
            'name' => $this->activity->title,
            'time' => date('Y-m-d H:i', strtotime($this->activity->package_get_time)),
            'address' => $this->activity->package_get_address,
        ];
    }
}
