<?php

/*
 * This file is part of guojiangclub/activity-backend.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Backend\Http\Controllers;

use Carbon\Carbon;
use DB;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use GuoJiangClub\Activity\Backend\Models\Activity;
use GuoJiangClub\Activity\Backend\Models\Discount;
use GuoJiangClub\Activity\Backend\Models\DiscountAction;
use GuoJiangClub\Activity\Backend\Models\DiscountCoupon;
use GuoJiangClub\Activity\Backend\Models\DiscountRule;
use iBrand\Backend\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class DiscountController extends Controller
{
    protected $discount;

    public function __construct(Discount $discount)
    {
        $this->discount = $discount;
    }

    public function index()
    {
        $status = request('status') ? 0 : 1;
        $orWhere = [];
        $where = [];

        if (1 == $status) {
            $where['status'] = $status;
            $where['ends_at'] = ['>=', Carbon::now()];
        } else {
            $orWhere['status'] = $status;
            $orWhere['ends_at'] = ['<', Carbon::now()];
        }

        if (request('type')) {
            $where['type'] = request('type');
        }

        if (request('title')) {
            $where['title'] = ['like', '%'.request('title').'%'];
        }

        $discount = $this->discount->getDiscountPaginate($where, $orWhere, 15);

        if (1 == $status) {
            $validCount = count($this->discount->getDiscountPaginate($where, $orWhere, 0));

            $invalidCount = $this->discount->getDiscountCountByStatus(0);
        } else {
            $validCount = $this->discount->getDiscountCountByStatus(1);

            $invalidCount = count($this->discount->getDiscountPaginate($where, $orWhere, 0));
        }

        $status_t = request('status');

        return Admin::content(function (Content $content) use ($discount, $validCount, $invalidCount, $status_t) {
            $content->description('优惠活动管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '优惠活动列表', 'no-pjax' => 1, 'left-menu-active' => '优惠活动列表']
            );

            $view = view('activity::discount.index', compact('discount', 'validCount', 'invalidCount', 'status_t'))->render();
            $content->row($view);
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->description('优惠活动管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '添加优惠活动', 'no-pjax' => 1, 'left-menu-active' => '添加优惠活动']
            );

            $view = view('activity::discount.create')->render();
            $content->row($view);
        });
    }

    public function edit($id)
    {
        $discount = Discount::find($id);

        return Admin::content(function (Content $content) use ($discount) {
            $content->description('修改优惠活动');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '修改优惠活动', 'no-pjax' => 1, 'left-menu-active' => '优惠活动列表']
            );

            $view = view('activity::discount.edit', compact('discount'))->render();
            $content->row($view);
        });
    }

    public function store(Request $request)
    {
        $base = $request->input('base');
        $rules = $request->input('rules');
        $action = $request->input('action');
        if (Activity::where('entry_end_at', '>', Carbon::now())->where('fee_type', 'PASS')->whereIn('status', [0, 1])->count() < 1) {
            return $this->ajaxJson(false, [], 404, '没有可用的指定活动');
        }

        $validate_rules = [
            'title' => 'required',
            'per_usage_limit' => 'required|integer|min:1',
            'usage_limit' => 'required|integer|min:1',
            'code' => $request->input('id') ? 'required|unique:ac_discount,code,'.$request->input('id') : 'required|unique:ac_discount,code',
        ];
        $message = [
            'required' => ':attribute 不能为空',
            'unique' => ':attribute 已存在',
            'integer' => ':attribute 必须为整数',
            'per_usage_limit.min' => ':attribute 必须大于0',
            'usage_limit.min' => ':attribute 必须大于0',
        ];
        $attributes = [
            'code' => '兑换码',
            'title' => '活动折扣名称',
            'per_usage_limit' => '每人可领取数量',
            'usage_limit' => '可用总数',
        ];
        $validator = Validator::make($base, $validate_rules, $message, $attributes);
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return $this->ajaxJson(false, [], 500, $show_warning);
        }

        try {
            DB::beginTransaction();

            if ($request->input('id')) {
                $discount = Discount::find(request('id'));
                $discount->fill($base);
                $discount->save();

                $discount->rules()->delete();

                $actionData = DiscountAction::find($request->input('action_id'));
                $actionData->fill($action);
                $actionData->save();
            } else {
                $discount = Discount::create($base);

                $action['discount_id'] = $discount->id;
                DiscountAction::create($action);
            }

            foreach ($rules as $key => $val) {
                $rulesData = [];
                $rulesData['discount_id'] = $discount->id;
                $rulesData['type'] = $val['type'];
                $rulesData['configuration'] = $val['value'];

                DiscountRule::create($rulesData);
            }

            DB::commit();

            return $this->ajaxJson(true, [], 200, '保存成功');
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->ajaxJson(false, [], 404, '保存失败');
        }
    }

    public function modalActivity()
    {
        // $url = 'activity.admin.discount.modal.getModalActivityData';
        return view('activity::discount.includes.modal.getActivity');
    }

    public function getModalActivityData()
    {
        $ids = explode(',', request('ids'));

        $activity = Activity::where('entry_end_at', '>', Carbon::now())
            ->where('fee_type', 'PASS')
            ->whereIn('status', [0, 1])
            ->paginate(10)
            ->toArray();
        $activity['ids'] = $ids;

        return $this->ajaxJson(true, $activity);
    }

    public function getSelectedActivity()
    {
        if ('all' == request('ids')) {
            return $this->ajaxJson(true, []);
        }

        $ids = explode(',', request('ids'));

        $activity = Activity::whereIn('id', $ids)
            ->get()
            ->toArray();

        return $this->ajaxJson(true, $activity);
    }

    public function switchStatus()
    {
        $id = request('id');
        $discount = Discount::find($id);

        if ($discount) {
            if ($discount->ends_at <= Carbon::now()) {
                return $this->ajaxJson(false, [], 404, '该优惠活动已过期');
            }

            if (0 == $discount->status) {
                $discount->status = 1;
            } else {
                $discount->status = 0;
            }

            $discount->save();

            return $this->ajaxJson(true, [], 200, '修改状态成功');
        }

        return $this->ajaxJson(false, [], 404, '促销方式不存在');
    }

    public function couponList($id)
    {
        $coupons = DiscountCoupon::where('discount_id', $id)->orderBy('updated_at', 'desc')->paginate(15);
        $discount = Discount::find($id);

        return Admin::content(function (Content $content) use ($coupons, $discount) {
            $content->description('优惠券明细表');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '优惠券明细表', 'no-pjax' => 1, 'left-menu-active' => '优惠活动列表']
            );

            $view = view('activity::discount.coupon_list', compact('coupons', 'discount'))->render();
            $content->row($view);
        });
    }
}
