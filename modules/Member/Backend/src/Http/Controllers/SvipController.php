<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/1/14
 * Time: 19:35
 */

namespace GuoJiangClub\Member\Backend\Http\Controllers;

use ElementVip\Component\Recharge\Models\RechargeRule;
use ElementVip\Member\Backend\Models\VipPlanRechargeRelation;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use iBrand\Backend\Http\Controllers\Controller;
use ElementVip\Member\Backend\Models\VipPlan;
use Illuminate\Support\Facades\Validator;

class SvipController extends Controller
{
    public function index()
    {
        $status = 1;
        if (request('status') == 'invalid') {
            $status = 0;
        }

        $query = VipPlan::where('status', $status);

        if (request('title')) {
            $query = $query->where('title', 'like', '%' . request('title') . '%');
        }
        $plans = $query->paginate(15);


        return LaravelAdmin::content(function (Content $content) use ($plans) {

            $content->header('VIP计划管理');

            $content->breadcrumb(
                ['text' => 'VIP计划管理', 'url' => '', 'no-pjax' => 1],
                ['text' => 'VIP计划列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => 'VIP计划管理']
            );

            $content->body(view('member-backend::vip_plan.index', compact('plans')));
        });
    }

    public function create()
    {
        $recharges = RechargeRule::where('status', 1)->get();

        return LaravelAdmin::content(function (Content $content) use ($recharges) {

            $content->header('添加VIP计划');

            $content->breadcrumb(
                ['text' => 'VIP计划管理', 'url' => '', 'no-pjax' => 1],
                ['text' => '添加VIP计划', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => 'VIP计划管理']
            );

            $content->body(view('member-backend::vip_plan.create', compact('recharges')));
        });
    }

    public function edit($id)
    {
        $plan = VipPlan::find($id);
        $recharges = RechargeRule::where('status', 1)->get();

        return LaravelAdmin::content(function (Content $content) use ($plan, $recharges) {

            $content->header('修改VIP计划');

            $content->breadcrumb(
                ['text' => 'VIP计划管理', 'url' => '', 'no-pjax' => 1],
                ['text' => '修改VIP计划', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => 'VIP计划管理']
            );

            $content->body(view('member-backend::vip_plan.edit', compact('plan', 'recharges')));
        });
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm();

        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return $this->ajaxJson(false, [], 500, $show_warning);
        }

        $data = $request->except(['_token', 'id', 'recharge_rule_id']);

        foreach ($data['actions'] as $key => $value) {
            if (!$value['link']) {
                $data['actions'][$key]['type'] = '';
                continue;
            }

            if (str_contains($value['link'], ['https', 'http'])) {
                $data['actions'][$key]['type'] = 'wechat';
            } else {
                $data['actions'][$key]['type'] = 'miniprogram';
            }
        }

        if ($request->input('id')) {
            $plan = VipPlan::find($request->input('id'));

            if ($plan->recharge->id != $request->input('recharge_rule_id')) {
                VipPlanRechargeRelation::where(['plan_id' => $plan->id, 'recharge_rule_id' => $plan->recharge->recharge_rule_id])->delete();
                VipPlanRechargeRelation::create(['plan_id' => $plan->id, 'recharge_rule_id' => $request->input('recharge_rule_id')]);
            }

            $plan->fill($data);
            $plan->save();


        } else {
            $plan = VipPlan::create($data);

            if ($plan) {
                VipPlanRechargeRelation::create(['plan_id' => $plan->id, 'recharge_rule_id' => $request->input('recharge_rule_id')]);
            }
        }

        return $this->ajaxJson();
    }

    protected function validateForm()
    {
        $rules = [
            'title' => 'required',
            'recharge_rule_id' => 'required'
        ];

        $message = [
            "required" => ":attribute 不能为空",
            "integer" => ":attribute 必须是整数",
            "numeric" => ":attribute 必须是数字",
            "between" => ':attribute 课程折扣值最小为1，最大为99'
        ];

        $attributes = [
            "title" => '活动名称',
            "recharge_rule_id" => '关联充值计划'
        ];

        $validator = Validator::make(request()->all(), $rules, $message, $attributes);

        return $validator;
    }

    public function settings()
    {
        return LaravelAdmin::content(function (Content $content) {

            $content->header('VIP设置');

            $content->breadcrumb(
                ['text' => 'VIP设置', 'url' => '', 'no-pjax' => 1],
                ['text' => 'VIP设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => 'VIP设置']
            );

            $content->body(view('member-backend::vip_plan.setting'));
        });
    }
}