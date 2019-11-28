<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/2/21
 * Time: 14:45
 */

namespace GuoJiangClub\Member\Backend\Http\Controllers;

use ElementVip\Component\Recharge\Models\RechargeRule;
use ElementVip\Member\Backend\Models\VipPlanRechargeRelation;
use ElementVip\Member\Backend\Repository\VipMemberRepository;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use iBrand\Backend\Http\Controllers\Controller;
use ElementVip\Member\Backend\Models\VipPlan;
use Illuminate\Support\Facades\Validator;

class SvipMemberController extends Controller
{
    protected $vipMemberRepository;
    protected $cache;

    public function __construct(VipMemberRepository $vipMemberRepository)
    {
        $this->vipMemberRepository = $vipMemberRepository;
        $this->cache = cache();
    }

    public function index()
    {
        $conditions = $this->conditions();
        $where = $conditions[0];
        $time = $conditions[1];

        $members = $this->vipMemberRepository->getMemberPaginate($where, $time, request('mobile'));
        $plans = VipPlan::all();

        return LaravelAdmin::content(function (Content $content) use ($members, $plans) {

            $content->header('VIP会员列表');

            $content->breadcrumb(
                ['text' => 'VIP会员管理', 'url' => '', 'no-pjax' => 1],
                ['text' => 'VIP会员列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => 'VIP会员管理']
            );

            $content->body(view('member-backend::vip_member.index', compact('members', 'plans')));
        });
    }

    protected function conditions()
    {
        $time = [];
        $where['is_default'] = 1;
        if (request('plan_id')) {
            $where['plan_id'] = request('plan_id');
        }

        if (!empty(request('etime')) && !empty(request('stime'))) {
            $where['joined_at'] = ['<=', request('etime')];
            $time['joined_at'] = ['>=', request('stime')];
        }

        if (!empty(request('etime'))) {
            $time['joined_at'] = ['<=', request('etime')];
        }

        if (!empty(request('stime'))) {
            $time['joined_at'] = ['>=', request('stime')];
        }

        return [$where, $time];
    }

    public function getExportData()
    {
        $page = request('page') ? request('page') : 1;
        $limit = request('limit') ? request('limit') : 15;
        $type = request('type');

        $condition = $this->conditions();
        $where = $condition[0];
        $time = $condition[1];

        $members = $this->vipMemberRepository->getMemberPaginate($where, $time, request('mobile'), $limit);

        $lastPage = $members->lastPage();

        $membersExcelData = $this->formatToExcelData($members);

        if ($page == 1) {
            session(['export_vip_member_cache' => generate_export_cache_name('export_vip_member_cache_')]);
        }
        $cacheName = session('export_vip_member_cache');

        if ($this->cache->has($cacheName)) {
            $cacheData = $this->cache->get($cacheName);
            $this->cache->put($cacheName, array_merge($cacheData, $membersExcelData), 300);
        } else {
            $this->cache->put($cacheName, $membersExcelData, 300);
        }

        if ($page == $lastPage) {
            $title = ['手机', '所属VIP计划', '加入时间', '支付金额(元)'];
            return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => $type, 'title' => $title, 'cache' => $cacheName, 'prefix' => 'vip_member_data_']);
        } else {
            $url_bit = route('admin.svip.member.getExportData', array_merge(['page' => $page + 1, 'limit' => $limit], request()->except('page', 'limit')));
            return $this->ajaxJson(true, ['status' => 'goon', 'url' => $url_bit, 'page' => $page, 'totalPage' => $lastPage]);
        }
    }

    public function formatToExcelData($members)
    {
        $data = [];
        if ($members->count()) {
            $i = 0;
            foreach ($members as $item) {
                $data[$i][] = $item->user ? $item->user->mobile : '/';
                $data[$i][] = $item->plan->title;
                $data[$i][] = $item->joined_at;
                $data[$i][] = $item->order->price / 100;
                $i++;
            }
        }
        return $data;
    }
}