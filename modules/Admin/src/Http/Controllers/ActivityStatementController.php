<?php

namespace GuoJiangClub\Activity\Admin\Http\Controllers;

use GuoJiangClub\Activity\Core\Models\Statement;
use iBrand\Backend\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Validator;

class ActivityStatementController extends Controller
{
	public function index()
	{
		$statements = Statement::orderBy('id', 'desc')->paginate(5);

		return Admin::content(function (Content $content) use ($statements) {
			$content->description('免责声明');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '免责声明', 'no-pjax' => 1, 'left-menu-active' => '免责声明']
			);

			$view = view('activity::statement.list', compact('statements'))->render();
			$content->row($view);
		});
	}

	public function curd($id = 0)
	{
		$model = null;
		if ($id) {
			$model = Statement::find($id);
		}

		return Admin::content(function (Content $content) use ($model) {
			$content->description('添加免责声明');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '免责声明', 'no-pjax' => 1, 'left-menu-active' => '免责声明']
			);

			$view = view('activity::statement.curd', compact('model'))->render();
			$content->row($view);
		});
	}

	public function store(Request $request)
	{
		$input      = $request->except('_token', 'file');
		$rules      = [
			'title'     => 'required',
			'statement' => 'required',
		];
		$message    = [
			'required' => ':attribute 不能为空',
		];
		$attributes = [
			'title'     => '标题',
			'statement' => '免责声明',
		];
		$validator  = Validator::make($input, $rules, $message, $attributes);
		if ($validator->fails()) {
			$warnings     = $validator->messages();
			$show_warning = $warnings->first();

			return $this->ajaxJson(false, [], 500, $show_warning);
		}

		if (strlen($input['title']) > 128) {
			return response()->json(['status' => false, 'code' => 500, 'message' => '标题超过字数限制']);
		}

		try {
			if ($input['id']) {
				Statement::where('id', $input['id'])->update(['statement' => $input['statement'], 'title' => $input['title']]);
			} else {
				Statement::create($input);
			}

			return $this->ajaxJson(true, [], 200, '保存成功');
		} catch (\Exception $exception) {
			\Log::info($exception->getMessage());

			return $this->ajaxJson(false, [], 500, '保存失败');
		}
	}

	public function delete($id)
	{
		Statement::where('id', $id)->delete();

		return $this->ajaxJson(true, [], 200, '删除成功');
	}
}