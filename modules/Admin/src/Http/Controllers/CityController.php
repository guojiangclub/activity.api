<?php

namespace GuojiangClub\Activity\Admin\Http\Controllers;

use ElementVip\Backend\Http\Controllers\Controller;
use ElementVip\Activity\Core\Models\City;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Validator;

class CityController extends Controller
{
	public function index()
	{
		$cities = City::paginate(10);

		return Admin::content(function (Content $content) use ($cities) {
			$content->description('城市管理');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '城市列表', 'no-pjax' => 1, 'left-menu-active' => '城市列表']
			);

			$view = view('activity::city.index', compact('cities'))->render();
			$content->row($view);
		});
	}

	public function create()
	{
		return Admin::content(function (Content $content) {
			$content->description('城市管理');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '创建城市', 'no-pjax' => 1, 'left-menu-active' => '城市列表']
			);

			$view = view('activity::city.create')->render();
			$content->row($view);
		});
	}

	public function edit($id)
	{
		$city = City::find($id);

		return Admin::content(function (Content $content) use ($city) {
			$content->description('城市编辑');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '城市编辑', 'no-pjax' => 1, 'left-menu-active' => '城市列表']
			);

			$view = view('activity::city.edit', compact('city'))->render();
			$content->row($view);
		});
	}

	public function store()
	{

		$input      = array_filter(request()->only(['name', 'img', 'province', 'city', 'area']));
		$rules      = [
			'name'     => 'required',
			'province' => 'required',
			'city'     => 'required',
			'area'     => 'required',
			'img'      => 'required',
		];
		$message    = [
			'required' => ':attribute 不能为空',
		];
		$attributes = [
			'name'     => '城市名称',
			'province' => '省份',
			'city'     => '城市',
			'area'     => '区域',
			'img'      => '图片',
		];
		$validator  = Validator::make($input, $rules, $message, $attributes);
		if ($validator->fails()) {
			$warnings     = $validator->messages();
			$show_warning = $warnings->first();

			return $this->ajaxJson(false, [], 500, $show_warning);
		}

		if (empty(request('id'))) {
			City::create($input);
		} else {
			$city = City::find(request('id'));
			$city->update($input);
		}

		return $this->ajaxJson();
	}

	public function delete($id)
	{
		City::find($id)->activity()->delete();
		City::find($id)->delete();

		return redirect(route('activity.admin.city'));
	}

}