<?php

/*
 * This file is part of ibrand/member-backend.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Member\Backend;

use GuoJiangClub\Member\Backend\Seeds\MemberBackendTablesSeeder;
use Encore\Admin\Admin;
use Encore\Admin\Extension;
use Illuminate\Support\Facades\Artisan;

class MemberBackend extends Extension
{
    /**
     * Bootstrap this package.
     */
    public static function boot()
    {
        Admin::extend('ibrand-member-backend', __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        Artisan::call('db:seed', ['--class' => MemberBackendTablesSeeder::class]);
    }
}
