<?php

namespace GuoJiangClub\Member\Backend\Console;

use Illuminate\Console\Command;
use DB;

class VipPlanMenus extends Command
{
	protected $signature = 'import:vip-plan-menus';

	protected $description = 'import vip plan menus';

	public function handle()
	{
		$lastOrder     = DB::table(config('admin.database.menu_table'))->max('order');
		$memberTopMenu = DB::table(config('admin.database.menu_table'))->where('title', '会员管理')->where('parent_id', 0)->first();

		$vipPlan = DB::table(config('admin.database.menu_table'))->where('title', 'VIP计划管理')->where('parent_id', $memberTopMenu->id)->first();
		if (!$vipPlan) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $memberTopMenu->id,
				'order'      => $lastOrder++,
				'title'      => 'VIP计划管理',
				'icon'       => 'fa-diamond',
				'blank'      => 1,
				'uri'        => 'member/svip/plan/list',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
	}
}
