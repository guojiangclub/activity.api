<?php

namespace GuojiangClub\Activity\Core\Schedule;

use Carbon\Carbon;
use GuojiangClub\Activity\Core\Models\Activity;
use GuojiangClub\Activity\Core\Notifications\Late;
use ElementVip\Scheduling\Schedule\Scheduling;

class LateSchedule extends Scheduling
{

    public function schedule()
    {

        //更新活动状态
        $this->schedule->call(function () {
            $activities = Activity::where('status', '<>', 0)->get();
            foreach ($activities as $activity) {
                if ($activity->entry_end_at > Carbon::now() AND $activity->starts_at > Carbon::now()) {
                    $activity->status = 1;
                } else if ($activity->entry_end_at < Carbon::now() AND $activity->starts_at > Carbon::now()) {
                    $activity->status = 4;
                } else if ($activity->starts_at < Carbon::now() AND $activity->ends_at > Carbon::now()) {
                    $activity->status = 2;
                } else if ($activity->ends_at < Carbon::now()) {
                    if ($activity->status == 2) {
                        $this->notify($activity);
                    }
                    $activity->status = 3;
                }
                $activity->save();
            }
        })->everyMinute();
    }

    private function notify($activity)
    {
        foreach ($activity->members as $member) {
            if (empty($member->signed_at) AND $member->status == 1 AND $member->role == 'user') {
                if ($user = $member->user)
                    /*$user->notify((new Late(['activity' => $activity]))->delay(Carbon::now()->addSeconds(10)));*/
                    $user->notify((new Late(['activity' => $activity]))->delay(Carbon::now()->addMinutes($activity->delay_sign + 1)));
            }
        }
    }

}