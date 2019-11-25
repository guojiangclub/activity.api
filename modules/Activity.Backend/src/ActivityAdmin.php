<?php

/*
 * This file is part of guojiangclub/activity-backend.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Backend;

use Encore\Admin\Admin;
use Encore\Admin\Extension;
use GuoJiangClub\Activity\Backend\Seeds\ActivityAdminSeeder;
use Illuminate\Support\Facades\Artisan;

class ActivityAdmin extends Extension
{
    /**
     * Bootstrap this package.
     */
    public static function boot()
    {
        Admin::extend('activity-admin', __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        Artisan::call('db:seed', ['--class' => ActivityAdminSeeder::class]);
    }
}
