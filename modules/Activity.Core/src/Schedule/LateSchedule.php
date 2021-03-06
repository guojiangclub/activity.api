<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Schedule;

use Carbon\Carbon;
use GuoJiangClub\Scheduling\Schedule\Scheduling;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Notifications\Late;

class LateSchedule extends Scheduling
{
    public function schedule()
    {
        //更新活动状态
        $this->schedule->call(function () {
            $activities = Activity::where('status', '<>', 0)->get();
            foreach ($activities as $activity) {
                if ($activity->entry_end_at > Carbon::now() and $activity->starts_at > Carbon::now()) {
                    $activity->status = 1;
                } elseif ($activity->entry_end_at < Carbon::now() and $activity->starts_at > Carbon::now()) {
                    $activity->status = 4;
                } elseif ($activity->starts_at < Carbon::now() and $activity->ends_at > Carbon::now()) {
                    $activity->status = 2;
                } elseif ($activity->ends_at < Carbon::now()) {
                    $activity->status = 3;
                }
                $activity->save();
            }
        })->everyMinute();
    }

}
