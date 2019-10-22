<?php

namespace GuoJiangClub\Activity\Admin;

use Encore\Admin\Admin;
use Encore\Admin\Extension;
use GuoJiangClub\Activity\Admin\Seeds\ActivityAdminSeeder;
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
