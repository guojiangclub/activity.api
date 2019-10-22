<?php

namespace GuojiangClub\Activity\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as NotificationExtend;
use ElementVip\Component\User\Models\UserBind;

class Notification extends NotificationExtend
{
    use Queueable;

    protected $day = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

    /**
     * Create a new notification instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    protected function checkOpenId($user)
    {
        $userBind = UserBind::byAppID($user->id, 'wechat',app('system_setting')->getSetting('activity_app_id'))->first();
        if (!$userBind OR empty($userBind->open_id)) {
            return false;
        }
        return true;
    }

    protected function getOpenId($user)
    {
        return UserBind::byAppID($user->id, 'wechat', app('system_setting')->getSetting('activity_app_id'))->first()->open_id;

    }

    protected function getFormatTime($starts_at, $ends_at)
    {
        $starts_at_time = strtotime($starts_at);
        $starts_at_day = $this->day[date('w', $starts_at_time)];
        $ends_at_time = strtotime($ends_at);
        $ends_at_day = $this->day[date('w', $ends_at_time)];
        if (date('Y-m-d', $starts_at_time) == date('Y-m-d', $ends_at_time))
            return date('Y/m/d', $starts_at_time) . ' ' . $starts_at_day . ' ' . date('H:i', $starts_at_time) . '-' . date('H:i', $ends_at_time);
        else
            return date('Y/m/d', $starts_at_time) . ' ' . $starts_at_day . ' ' . date('H:i', $starts_at_time) . ' - ' .
                date('Y/m/d', $ends_at_time) . ' ' . $ends_at_day . ' ' . date('H:i', $ends_at_time);
    }

}
