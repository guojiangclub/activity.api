<?php

namespace GuoJiangClub\Activity\Admin\Http\Controllers;

use GuoJiangClub\Activity\Admin\Models\Activity;
use GuoJiangClub\Activity\Core\Models\ActivityCategory;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use iBrand\Backend\Http\Controllers\Controller;

class ActivityCategoryController extends Controller
{
	use ModelForm;

	public function index()
	{
		return Admin::content(function (Content $content) {
			$content->description('活动分类');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '活动分类', 'no-pjax' => 1, 'left-menu-active' => '活动分类']
			);

			$content->body($this->grid()->render());
		});
	}

	public function create()
	{
		return Admin::content(function (Content $content) {
			$content->description('添加活动分类');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '活动分类']
			);

			$content->body($this->form());
		});
	}

	public function edit($id)
	{
		return Admin::content(function (Content $content) use ($id) {
			$content->description('添加活动分类');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '活动分类']
			);

			$content->body($this->form()->edit($id));
		});
	}

	public function form()
	{
		return Admin::form(ActivityCategory::class, function (Form $form) {
			$form->text('name', '分类名称')->rules('required', ['name.required' => '请填写 分类名称']);

			$form->tools(function (Form\Tools $tools) {
				$tools->disableBackButton();
			});
			$form->disableReset();

			$form->tools(function (Form\Tools $tools) {
				$tools->disableDelete();
				$tools->disableView();
			});
		});
	}

	public function grid()
	{
		return Admin::grid(ActivityCategory::class, function (Grid $grid) {
			$grid->id('id')->sortable();
			$grid->column('name', '分类名称');
			$grid->disableExport();
			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('name', '分类名称');
			});

			$grid->actions(function ($actions) {
				$actions->disableView();
			});
		});
	}

	public function delete($id)
	{
		$check = Activity::where('category_id', $id)->first(['id']);
		if ($check) {

			return $this->ajaxJson(false, [], 500, '该分类下已添加活动，无法删除');
		}

		ActivityCategory::where('id', $id)->delete();

		return $this->ajaxJson(true, [], 200, '删除成功');
	}
}