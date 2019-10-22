<?php

namespace GuoJiangClub\Activity\Admin\Http\Controllers;

use GuoJiangClub\Activity\Admin\Models\Activity;
use GuoJiangClub\Activity\Core\Models\ActivityForm;
use iBrand\Backend\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Validator;

class ActivityFormController extends Controller
{
	public function index()
	{
		$forms = ActivityForm::orderBy('id', 'desc')->paginate(10);

		return Admin::content(function (Content $content) use ($forms) {
			$content->description('活动报名表单');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '活动报名表单', 'no-pjax' => 1, 'left-menu-active' => '活动报名表单']
			);

			$view = view('activity::form.list', compact('forms'))->render();
			$content->row($view);
		});
	}

	public function curd($id = 0)
	{
		$model   = null;
		$nameArr = [];
		$fields  = [];
		if ($id) {
			$model  = ActivityForm::find($id);
			$fields = json_decode($model->fields, true);
			if (!empty($fields)) {
				foreach ($fields as $field) {
					if (in_array($field['name'], ['province', 'id_card', 'user_name', 'mobile', 'other_certificate', 'certificate_type'])) {
						array_push($nameArr, $field['name']);
					}
				}

				if (!empty($nameArr)) {
					$nameArr = json_encode($nameArr);
				}
			}
		}

		return Admin::content(function (Content $content) use ($model, $fields, $nameArr) {
			$content->description('活动报名表单');

			$content->breadcrumb(
				['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
				['text' => '活动报名表单', 'no-pjax' => 1, 'left-menu-active' => '活动报名表单']
			);

			$view = view('activity::form.curd', compact('model', 'fields', 'nameArr'))->render();
			$content->row($view);
		});
	}

	public function store(Request $request)
	{
		$input   = $request->except('_token', 'file');
		$rules   = [
			'name'                 => $input['id'] ? 'required|unique:ac_activity_form_fields,name,' . $input['id'] : 'required|unique:ac_activity_form_fields,name',
			'activityForm'         => 'required',
			'activityForm.*.title' => 'required|alpha_dash',
			'activityForm.*.name'  => 'required|alpha_dash',
		];
		$message = [
			'required'                        => ':attribute 不能为空',
			'unique'                          => ':attribute 已存在',
			'activityForm.*.name.alpha_dash'  => ':attribute 只能包含字母、数字、下划线_',
			'activityForm.*.title.alpha_dash' => ':attribute 只能包含汉字、字母、数字、下划线_',
		];

		$attributes = [
			'name'                 => '表单名称',
			'activityForm'         => '活动表单',
			'activityForm.*.title' => '字段名称',
			'activityForm.*.name'  => '字段name属性值',
		];
		$validator  = Validator::make($input, $rules, $message, $attributes);
		if ($validator->fails()) {
			$warnings     = $validator->messages();
			$show_warning = $warnings->first();

			return response()->json(['status' => false, 'code' => 500, 'message' => $show_warning]);
		}

		if (strlen($input['name']) > 128) {
			return response()->json(['status' => false, 'code' => 500, 'message' => '表单名称超过字数限制']);
		}

		try {
			$fields = $this->createFormFields($input['activityForm']);
			if ($input['id']) {
				ActivityForm::where('id', $input['id'])->update(['fields' => json_encode($fields), 'name' => $input['name']]);
			} else {
				if (!empty($fields)) {
					ActivityForm::create(['fields' => json_encode($fields), 'name' => $input['name']]);
				}
			}

			return $this->ajaxJson(true, [], 200, '保存成功');
		} catch (\Exception $exception) {
			\Log::info($exception->getMessage());

			return $this->ajaxJson(false, [], 500, '保存失败');
		}
	}

	public function createFormFields($fields)
	{
		$data = [];
		if (!empty($fields)) {
			foreach ($fields as $k => $field) {
				if (!$field['title']) {
					unset($fields[$k]);
					continue;
				}

				if (!isset($field['is_necessary'])) {
					$field['is_necessary'] = 0;
				}

				if (!isset($field['status']) || $field['status'] == 0) {
					$field['is_necessary'] = 0;
				}

				$arr = [];
				if (!empty($field['options']) && !empty($field['options']['name'])) {
					foreach ($field['options']['name'] as $key => $value) {
						if ($field['name'] != 'certificate_type') {
							$arr[] = ['name' => $value, 'index' => $field['options']['index'][$key]];
						} else {
							$arr[] = ['name' => $value, 'index' => $field['options']['index'][$key], 'value' => $field['options']['value'][$key]];
						}
					}

					$field['options'] = $arr;
				}

				$data[] = $field;
			}
		}

		return $data;
	}

	public function delete($id)
	{
		$check = Activity::where('form_id', $id)->first(['id']);
		if ($check) {

			return $this->ajaxJson(false, [], 500, '已有活动选择该表单，无法删除');
		}

		ActivityForm::where('id', $id)->delete();

		return $this->ajaxJson(true, [], 200, '删除成功');
	}
}