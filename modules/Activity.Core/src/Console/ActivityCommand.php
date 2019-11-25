<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Console;

use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Models\City;
use GuoJiangClub\Activity\Core\Models\Payment;
use Illuminate\Console\Command;

class ActivityCommand extends Command
{
    protected $signature = 'activity:factory';

    protected $description = 'Activity factory.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $city = factory(City::class, 'city', 1)->create();

        $activity = factory(Activity::class, 'activity')->create([
            'city_id' => $city->id,
            'address' => '长沙市开福区新时代广场',
            'member_limit' => 25,
            'status' => 0,
        ]);
        factory(Payment::class, 'payment')->create([
            'activity_id' => $activity->id,
            'type' => 0,
            'point' => 1000,
        ]);

        $activity = factory(Activity::class, 'activity')->create([
            'city_id' => $city->id,
            'status' => 1,
        ]);
        factory(Payment::class, 'payment')->create([
            'activity_id' => $activity->id,
            'type' => 1,
            'price' => 10000,
        ]);

        $activity = factory(Activity::class, 'activity')->create([
            'city_id' => $city->id,
            'status' => 2,
        ]);
        factory(Payment::class, 'payment')->create([
            'activity_id' => $activity->id,
            'type' => 2,
            'price' => 10000,
            'point' => 1000,
        ]);

        $activity = factory(Activity::class, 'activity')->create([
            'city_id' => $city->id,
            'status' => 3,
        ]);
        factory(Payment::class, 'payment')->create([
            'activity_id' => $activity->id,
            'type' => 2,
            'price' => 10000,
            'point' => 1000,
        ]);
    }
}
