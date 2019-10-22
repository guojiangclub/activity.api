<?php

namespace GuojiangClub\Activity\Admin\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityAdminSeeder extends Seeder
{
	public function run()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');

		$parent = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => 0,
			'order'      => $lastOrder++,
			'title'      => '体验管理',
			'icon'       => 'fa-tachometer',
			'blank'      => 1,
			'uri'        => 'activity',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		$first = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $parent,
			'order'      => $lastOrder++,
			'title'      => '活动内容管理',
			'icon'       => 'fa-connectdevelop',
			'blank'      => 1,
			'uri'        => '',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $first,
			'order'      => $lastOrder++,
			'title'      => '城市列表',
			'icon'       => 'fa-list-alt',
			'blank'      => 1,
			'uri'        => 'activity/activity-city',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $first,
			'order'      => $lastOrder++,
			'title'      => '活动列表',
			'icon'       => 'fa-th-list',
			'blank'      => 1,
			'uri'        => 'activity/activity-list',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $first,
			'order'      => $lastOrder++,
			'title'      => '活动发布',
			'icon'       => 'fa-caret-square-o-right',
			'blank'      => 1,
			'uri'        => 'activity/activity-create',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $first,
			'order'      => $lastOrder++,
			'title'      => '活动报名表单',
			'icon'       => 'fa-folder-o',
			'blank'      => 1,
			'uri'        => 'activity/activity-form',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $first,
			'order'      => $lastOrder++,
			'title'      => '活动分类',
			'icon'       => 'fa-calendar-check-o',
			'blank'      => 1,
			'uri'        => 'activity/activity-category',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $first,
			'order'      => $lastOrder++,
			'title'      => '免责声明',
			'icon'       => 'fa-stack-exchange',
			'blank'      => 1,
			'uri'        => 'activity/activity-statement',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $first,
			'order'      => $lastOrder++,
			'title'      => '教练管理',
			'icon'       => 'fa-y-combinator',
			'blank'      => 1,
			'uri'        => 'activity/activity-coach',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		$second = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $parent,
			'order'      => $lastOrder++,
			'title'      => '活动订单管理',
			'icon'       => 'fa-adn',
			'blank'      => 1,
			'uri'        => 'fa-align-justify',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $second,
			'order'      => $lastOrder++,
			'title'      => '订单列表',
			'icon'       => 'fa-reorder',
			'blank'      => 1,
			'uri'        => 'activity/activityOrder-list',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $second,
			'order'      => $lastOrder++,
			'title'      => '退款管理',
			'icon'       => 'fa-refresh',
			'blank'      => 1,
			'uri'        => 'activity/refund',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		$third = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $parent,
			'order'      => $lastOrder++,
			'title'      => '活动营销管理',
			'icon'       => 'fa-bookmark',
			'blank'      => 1,
			'uri'        => '',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $third,
			'order'      => $lastOrder++,
			'title'      => '优惠活动列表',
			'icon'       => 'fa-list-ol',
			'blank'      => 1,
			'uri'        => 'activity/discount',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $third,
			'order'      => $lastOrder++,
			'title'      => '添加优惠活动',
			'icon'       => 'fa-creative-commons',
			'blank'      => 1,
			'uri'        => 'activity/discount/create',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);
	}
}