<?php

namespace GuojiangClub\Activity\Core\Schedule;

use Carbon\Carbon;
use GuojiangClub\Activity\Core\Models\Activity;
use GuojiangClub\Activity\Core\Notifications\Remind;
use ElementVip\Scheduling\Schedule\Scheduling;

class RemindSchedule extends Scheduling
{


    public function schedule()
    {


        $this->schedule->call(function ()  {
            $activities = Activity::where('status', 1)->get();

            foreach ($activities as $activity) {

                foreach ($activity->members as $member) {
                    if (empty($member->signed_at)
                        AND $member->status == 1
                        AND $member->role == 'user'
                        AND empty($member->remind_at)
                        AND $user = $member->user
                    ) {

                        if (Carbon::now()->addDay(1) > $activity->starts_at) {
                            // 加一天时间大于开始时间，说明离开始时间已经不够24小时，否则不放入队列
                            // 5分钟后就执行队列
                            $delay = Carbon::now()->addMinutes(5);
                            $user->notify((new Remind(['activity' => $activity]))->delay($delay));
                        }
                    }
                }
            }

            /*if (count($activities) > 0) {
                $filter = $activities->filter(function ($value, $key) {
                    $before = date('Y-m-d', strtotime($value->starts_at . '-1 day'));
                    $now = date('Y-m-d');
                    return $before == $now;
                })->all();

                if (count($filter) > 0) {
                    foreach ($filter as $activity) {
                        $this->notify($activity);
                    }
                }
            }*/


        })->everyTenMinutes();


        /*$this->schedule->call(function () use ($goodsHandle) {
            \Log::info('ftp定时任务商品已进入1');
            $goodsHandle->handle();
        })->dailyAt('3:10');*/
    }

    private function notify($activity)
    {
        $delay = Carbon::now()->addMinutes($this->delay($activity->starts_at));

        foreach ($activity->members as $member) {
            if (empty($member->signed_at) AND $member->status == 1 AND $member->role == 'user') {
                if ($user = $member->user)
                    $user->notify((new Remind(['activity' => $activity]))->delay($delay));
            }
        }
    }

    private function delay($time)
    {
        $diff_time = strtotime($time) - strtotime(Carbon::now());
        return round($diff_time / 60);

    }

}