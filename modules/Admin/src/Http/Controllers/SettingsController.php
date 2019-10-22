<?php

namespace GuojiangClub\Activity\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;

class SettingsController extends Controller
{

	public function domain()
	{
		return Admin::content(function (Content $content) {
			$content->description('体验管理设置');

			$view = view('activity::settings.domain')->render();

			$content->body($view);
		});
	}

	public function domainStore()
	{
		$input = array_filter(request()->except(['_token']));
		settings()->setSetting($input);

		return response()->json(['status' => true]);
	}

}