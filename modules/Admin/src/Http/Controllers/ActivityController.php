<?php

namespace GuoJiangClub\Activity\Admin\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\Activity\Admin\Models\Discount;
use GuoJiangClub\Activity\Core\Models\ActivityCategory;
use GuoJiangClub\Activity\Core\Models\ActivityForm;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Models\Payment;
use GuoJiangClub\Activity\Core\Models\Role;
use GuoJiangClub\Activity\Core\Models\Statement;
use GuoJiangClub\Activity\Core\Notifications\Rewards;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use iBrand\Backend\Http\Controllers\Controller;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use iBrand\Component\Point\Repository\PointRepository;
use iBrand\Component\User\Models\User;
use Validator;
use GuoJiangClub\Activity\Core\Models\City;
use GuoJiangClub\Activity\Core\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use GuoJiangClub\Activity\Core\Repository\ActivityFormRepository;
use Response;
use DB;

class ActivityController extends Controller
{

    protected $role;
    protected $activity;
    protected $activityRepository;
    protected $point;
    protected $activityForm;

    /**
     * ActivityController constructor.
     *
     * @param Role $role
     * @param Activity $activity
     * @param PointRepository $pointRepository
     * @param ActivityRepository $activityRepository
     */
    public function __construct(Role $role,
                                Activity $activity,
                                PointRepository $pointRepository,
                                ActivityRepository $activityRepository,
                                ActivityFormRepository $activityFormRepository)
    {
        $this->activity = $activity;
        $this->activityRepository = $activityRepository;
        $this->point = $pointRepository;
        $this->activityForm = $activityFormRepository;
        $this->role = $role;
    }

    public function index()
    {
        $time = request()->input('time');
        $status = request()->input('status');
        $where = [];
        if (request('id')) {
            $where[] = ['id', '=', request('id')];
        }

        if (!empty($time) && $time != -1) {
            $where[] = ['ends_at', '<=', date('Y-m-d 23:59:59')];
            $where[] = ['starts_at', '>=', date('Y-m-d 00:00:00', strtotime('-' . $time . ' day'))];
        } else {
            if (request()->input('starts_at')) {
                $starts_at = request()->input('starts_at') . ' 00:00:00';
                $where[] = ['starts_at', '>=', $starts_at];
            }

            if (request()->input('ends_at')) {
                $ends_at = request()->input('ends_at') . ' 23:59:59';
                $where[] = ['ends_at', '<=', $ends_at];
            }
        }

        if (!empty($status) && $status != -1) {
            $where[] = ['status', '=', $status];
        }

        if (request('excel') == 1) {
            $file = $this->getDataExcel($this->activity, $where);

            return Response::download(storage_path() . "/exports/" . $file);
        }

        $activities = $this->getData($this->activity, $where);

        return Admin::content(function (Content $content) use ($activities) {
            $content->description('活动管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '活动列表', 'no-pjax' => 1, 'left-menu-active' => '活动列表']
            );

            $view = view('activity::activity.activityList', compact('activities'))->render();
            $content->row($view);
        });
    }

    public function create()
    {
        $city = City::get();
        $coachArray = [];
        $coach = $this->role->where("name", "coach")->first();
        $statements = Statement::all(['id', 'title']);
        $categories = ActivityCategory::all(['id', 'name']);
        $forms = ActivityForm::all(['id', 'name']);
        if ($coach) {
            $prefix = config('ibrand.app.database.prefix', 'ibrand_');
            $users = DB::table($prefix . 'role_user')->where('role_id', $coach->id)->get(['user_id']);
            if (count($users) > 0) {
                $usersId = array_column($users->toArray(), 'user_id');
                $coachArray = User::whereIn('id', $usersId)->get();
            }
        }

        return Admin::content(function (Content $content) use ($city, $coachArray, $statements, $categories, $forms) {
            $content->description('活动管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '活动发布', 'no-pjax' => 1, 'left-menu-active' => '活动发布']
            );

            $view = view("activity::activity.activityCreate", compact('city', 'coachArray', 'statements', 'categories', 'forms'))->render();
            $content->row($view);
        });
    }

    public function store()
    {
        // 插入 activity 信息

        $input = request()->only('title', 'subtitle', 'share_title', 'description', 'content', 'city_id', 'img', 'img_list', 'address', 'address_name', 'address_point', 'member_limit', 'starts_at', 'ends_at', 'entry_end_at', 'member_count', 'refund_status', 'refund_term', 'refund_text', 'status', 'finish_min_hours', 'finish_min_minutes', 'finish_max_hours', 'finish_max_minutes', 'statement_id', 'category_id', 'form_id', 'send_message', 'package_get_address', 'package_get_time');

        $input['member_limit'] = $input['member_limit'] == '' ? null : $input['member_limit'];
        $input['package_get_address'] = $input['package_get_address'] == '' ? null : $input['package_get_address'];
        $input['package_get_time'] = $input['package_get_time'] == '' ? null : $input['package_get_time'];
        $input['delay_sign'] = request('delay_sign') == '' ? 0 : request('delay_sign');
        $input['fee_type'] = request('activity_payment_radio');
        if ($input['status'] == 1) {
            $input['published_at'] = Carbon::now();
        }

        if (!isset($input['refund_term']) || !$input['refund_term']) {
            $input['refund_term'] = 0;
        }

        if (!isset($input['member_count']) || !$input['member_count']) {
            $input['member_count'] = 0;
        }

        // 表单验证
        $validator = $this->validateForm($input['fee_type']);
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return response()->json(['status' => false, 'error_code' => 0, 'error' => $show_warning]);
        }

        // 插入 activity 支付方式
        $paymentPostIds = request()->input('activity-payment-id');
        $title = request()->input('activity-payment-title');
        $point = request()->input('activity-payment-point');
        $price = request()->input('activity-payment-price');
        $limit = request()->input('activity-payment-limit');
        if ($input['fee_type'] == 'CHARGING' && (empty($title) || empty($point) || empty($price) || empty($limit))) {
            return response()->json(['status' => false, 'error_code' => 0, 'error' => '必须添加一张电子票']);
        }

        if ($input['fee_type'] == 'PASS') {
            $input['refund_status'] = 1;
        }

        try {
            DB::beginTransaction();
            $activity = $this->activity->create($input);

            // 报名费用
            if ($input['fee_type'] == 'PASS') {
                $activity->payments()->create(['type' => 3, 'title' => '', 'limit' => 0]);
            } elseif ($input['fee_type'] == 'OFFLINE_CHARGES') {
                $activity->payments()->create(['type' => 4, 'title' => '', 'limit' => 0, 'price' => request('payment-offline-charge')]);
            } else {
                $this->createPayment($activity, $paymentPostIds, $title, $price, $point, $limit);
            }

            // 活动的教练
            $coachIds = request()->input('user_id');
            if (!empty($coachIds)) {
                $this->storeCoach($activity, $coachIds);
            }

            $this->storePoint($activity);

            DB::commit();

            return response()->json(['status' => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage());
            return response()->json(['status' => false, 'error' => '发布失败']);
        }
    }

    public function update($id)
    {
        // 插入 activity 信息
        $input = request()->only('title', 'subtitle', 'share_title', 'description', 'content', 'city_id', 'img', 'img_list', 'address', 'address_name', 'address_point', 'member_limit', 'starts_at', 'ends_at', 'entry_end_at', 'member_count', 'refund_status', 'refund_term', 'refund_text', 'status', 'finish_min_hours', 'finish_min_minutes', 'finish_max_hours', 'finish_max_minutes', 'statement_id', 'category_id', 'form_id', 'send_message', 'package_get_address', 'package_get_time');

        $input['member_limit'] = $input['member_limit'] == '' ? null : $input['member_limit'];
        $input['package_get_address'] = $input['package_get_address'] == '' ? null : $input['package_get_address'];
        $input['package_get_time'] = $input['package_get_time'] == '' ? null : $input['package_get_time'];
        $input['delay_sign'] = request('delay_sign') == '' ? 0 : request('delay_sign');
        if (!isset($input['refund_term']) || !$input['refund_term']) {
            $input['refund_term'] = 0;
        }

        if (!isset($input['member_count']) || !$input['member_count']) {
            $input['member_count'] = 0;
        }

        $activity = $this->activity->find($id);

        $activity_status = $activity->status;
        $status = $activity->status;

        // 表单验证
        $validator = $this->validateForm($activity->fee_type);
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return response()->json(['status' => false, 'error_code' => 0, 'error' => $show_warning]);
        }

        if ($activity_status == 0 AND $input['status'] == 1) {

            $input['published_at'] = Carbon::now();
        }

        $input['fee_type'] = $activity->fee_type;
        if ($input['fee_type'] == 'PASS') {
            $input['refund_status'] = 1;
        }

        $title = request()->input('activity-payment-title');
        $point = request()->input('activity-payment-point');
        $price = request()->input('activity-payment-price');
        $limit = request()->input('activity-payment-limit');
        $status = request()->input('activity-payment-status');
        if ($activity->fee_type == 'CHARGING' && (empty($title) || empty($point) || empty($price) || empty($limit))) {
            return response()->json(['status' => false, 'error_code' => 0, 'error' => '必须添加一张电子票']);
        }

        try {
            DB::beginTransaction();
            $activity->update($input);
            if ($input['fee_type'] == 'CHARGING') {
                $paymentIds = $activity->payments()->pluck('id')->toArray();
                $paymentPostIds = request()->input('activity-payment-id');
                $paymentUpdateIds = [];
                $paymentCreateIds = [];
                $paymentDeleteIds = [];
                foreach ($paymentPostIds as $key => $id) {
                    if (in_array($id, $paymentIds)) {
                        $paymentUpdateIds[] = $id;
                    } elseif (!in_array($id, $paymentIds) && strpos($id, 'new_') !== false) {
                        $paymentCreateIds[$key] = substr($id, 4);
                    }
                }

                foreach ($paymentIds as $id) {
                    if (!in_array($id, $paymentPostIds) && strpos($id, 'new_') === false) {
                        $check = Member::where('payment_id', $id)->whereIn('status', [0, 1])->first();
                        if ($check) {
                            $payment_info = Payment::find($id);
                            DB::rollBack();

                            return response()->json(['status' => false, 'error_code' => 0, 'error' => '电子票: ' . $payment_info->title . ' 已经有人报名， 无法删除']);
                            break;
                        }

                        $paymentDeleteIds[] = $id;
                    }
                }

                if (!empty($paymentDeleteIds)) {
                    $activity->payments()->whereIn('id', $paymentDeleteIds)->delete();
                }

                if (!empty($paymentCreateIds)) {
                    $this->createPayment($activity, $paymentCreateIds, $title, $price, $point, $limit);
                }

                $this->updatePayment($activity, $paymentUpdateIds, $title, $price, $point, $limit, $status);
            }

            //修改线下支付金额
            if ($input['fee_type'] == 'OFFLINE_CHARGES') {
                $activity->payments()->delete();
                $activity->payments()->create(['type' => 4, 'title' => '', 'limit' => 0, 'price' => request('payment-offline-charge')]);
            }

            //PASS支付
            if ($input['fee_type'] == 'PASS') {
                $activity->payments()->delete();
                $activity->payments()->create(['type' => 3, 'title' => '', 'limit' => 0]);
            }

            $coachIds = request()->input('user_id');
            if (!empty($coachIds)) {
                $this->storeCoach($activity, $coachIds);
            } else {
                $activity->members()->where('role', 'coach')->delete();
            }

            $this->storePoint($activity);

            DB::commit();

            return response()->json(['status' => true]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json(['status' => false, 'error_code' => 0, 'error' => $exception->getMessage()]);
        }
    }

    public function delete($id)
    {
        $activity = $this->activity->find($id);
        // 删除 与 activity 相关的
        $activity->Members()->delete();
        $activity->payments()->delete();
        // 删除 activity
        $activity->delete();

        return response()->json(["status" => true]);
    }

    public function publishActivity($id)
    {
        $activity = $this->activity->find($id);
        $activity->update([
            'status' => 1,
            'published_at' => Carbon::now(),
        ]);

        return response()->json(["status" => true]);
    }

    public function edit($id)
    {
        $model = $this->activity->find($id);
        $city = City::get();
        $statements = Statement::all(['id', 'title']);
        $categories = ActivityCategory::all(['id', 'name']);
        $forms = ActivityForm::all(['id', 'name']);
        $coachArray = [];
        $coach = $this->role->where("name", "coach")->first();
        if ($coach) {
            $users = DB::table('el_role_user')->where('role_id', $coach->id)->get(['user_id']);
            if (count($users) > 0) {
                $usersId = array_column($users->toArray(), 'user_id');
                $coachArray = User::whereIn('id', $usersId)->get();
            }
        }

        $payment = $model->payments()->get();
        $selectedCoach = $model->Members()->where("role", 'coach')->get();
        $points = $model->points;
        $point['join'] = $points->where('type', 'act_join')->first();
        $point['sign'] = $points->where('type', 'act_sign')->first();
        $point['rewards'] = $points->where('type', 'coach_rewards')->first();

        return Admin::content(function (Content $content) use ($model, $city, $coachArray, $statements, $categories, $forms, $payment, $selectedCoach, $point) {
            $content->description('活动管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '编辑活动', 'no-pjax' => 1, 'left-menu-active' => '活动发布']
            );

            $view = view("activity::activity.activityEdit", compact('model', 'city', 'coachArray', 'statements', 'categories', 'forms', 'payment', 'selectedCoach', 'point'))->render();
            $content->row($view);
        });
    }

    protected function getData($object, $where, $limit = 25)
    {
        $data = $object->where(function ($query) use ($where) {
            if (is_array($where)) {
                foreach ($where as $value) {
                    if (is_array($value)) {
                        list($condition, $operate, $va) = $value;
                        $query = $query->where($condition, $operate, $va);
                    }
                }
            }

            return $query;
        });

        if ($limit == 0) {
            return $data->orderBy('created_at', 'desc')->get();
        } else {
            return $data->orderBy('created_at', 'desc')->paginate($limit);
        }
    }

    protected function getDataExcel($object, $where)
    {
        $data = $object->where(function ($query) use ($where) {
            if (is_array($where)) {
                foreach ($where as $value) {
                    if (is_array($value)) {
                        list($condition, $operate, $va) = $value;
                        $query = $query->where($condition, $operate, $va);
                    }
                }
            }

            return $query;
        });

        $data = $data->select([
            'id',
            'status',
            'title',
            'subtitle',
            'city_id',
            'address',
            'member_limit',
            'member_count',
            'like_count',
            'difficult',
            'starts_at',
            'ends_at',
            'entry_end_at',
            'refund_term',
            'refund_text',
            'content',
        ]);
        $data = $data->orderBy('id', 'desc')->get()->toArray();
        foreach ($data as &$item) {
            $item['content'] = strip_tags($item['content']);
            if ($city = City::withTrashed()->where('id', $item['city_id'])->first()) {
                $item['city_id'] = $city->name;
            }

            switch ($item['status']) {
                case 0 :
                    $item['status'] = '审核中';
                    break;
                case 1 :
                    $item['status'] = '报名中';
                    break;
                case 2 :
                    $item['status'] = '进行中';
                    break;
                case 3 :
                    $item['status'] = '已结束';
                    break;
                case 4 :
                    $item['status'] = '截止报名';
                    break;
                default :
                    break;
            }

            unset($item['coach']);
            unset($item['signed_count']);
            unset($item['can_reward']);
            unset($item['can_reward_limit']);
            unset($item['can_sign']);
        }
        $title = [
            'ID',
            '活动状态',
            '标题',
            '副标题',
            '城市名',
            '地址',
            '人数限制',
            '报名人数',
            '喜欢人数',
            '难度',
            '开始时间',
            '结束时间',
            '报名截止日期',
            '距离活动开始X分钟可以退款',
            '退款说明',
            '活动介绍',
        ];
        $name = 'Activities_' . date('Y_m_d_H_i_s', time());
        Excel::create($name, function ($excel) use ($data, $title) {
            $excel->sheet('活动列表', function ($sheet) use ($data, $title) {
                $sheet->prependRow(1, $title);
                $sheet->rows($data);
                $sheet->setWidth([
                    'A' => 5,
                    'B' => 10,
                    'C' => 30,
                    'D' => 30,
                    'E' => 10,
                    'F' => 30,
                    'G' => 10,
                    'H' => 10,
                    'I' => 10,
                    'J' => 10,
                    'K' => 20,
                    'L' => 20,
                    'M' => 20,
                    'N' => 30,
                    'O' => 20,
                    'P' => 100,
                ]);
            });
        })->store('xls');

        return "$name.xls";
    }

    protected function validateForm($fee_type)
    {
        $rules = [
            'title' => 'required|alpha_dash',
            'img' => 'required',
            'img_list' => 'required',
            'starts_at' => 'required|date ',
            'ends_at' => 'required|date|after:starts_at',
            'entry_end_at' => 'required|date',
            'city_id' => 'required|integer',
            'address' => 'required',
            'address_name' => 'required',
            'address_point' => 'required',
            'content' => 'required',
            'finish_min_hours' => 'required|integer|min:0',
            'finish_min_minutes' => 'required|integer|min:0',
            'finish_max_hours' => 'required|integer|min:0',
            'finish_max_minutes' => 'required|integer|min:0',
        ];

        $message = [
            'required' => ':attribute 不能为空',
            'alpha_dash' => ':attribute 只能包含汉字、字母、数字、下划线_',
            'filled' => ':attribute 不能为空',
            'address_point.required' => ':attribute无效 请重新选择活动地点',
            'ends_at.after' => ':attribute 不能早于 活动开始时间',
            'entry_end_at.before' => ':attribute 不能晚于 活动开始时间',
            'package_get_time.before' => ':attribute 不能晚于 活动开始时间',
            'city_id.integer' => '请选择:attribute',
            'img.required' => '请上传详情图片',
            'img_list.required' => '请上传列表图片',
            'integer' => ':attribute 只能为整数',
            'finish_min_hours.min' => ':attribute 必须大于等于0',
            'finish_min_minutes.min' => ':attribute 必须大于等于0',
            'finish_max_hours.min' => ':attribute 必须大于等于0',
            'finish_max_minutes.min' => ':attribute 必须大于等于0',
            'activity-payment-price.*.min' => ':attribute 必须大于等于0',
            'activity-payment-point.*.min' => ':attribute 必须大于等于0',
            'activity-payment-limit.*.min' => ':attribute 必须大于等于0',
        ];

        $attributes = [
            'title' => '活动名称',
            'starts_at' => '活动开始时间',
            'ends_at' => '活动结束时间',
            'entry_end_at' => '报名截止时间',
            'address' => '活动地点',
            'address_name' => '活动地点',
            'address_point' => '活动坐标',
            'city_id' => '活动城市',
            'content' => '活动详情',
            'finish_min_hours' => '目标完成时间最小时间',
            'finish_min_minutes' => '目标完成时间最小时间',
            'finish_max_hours' => '目标完成时间最大时间',
            'finish_max_minutes' => '目标完成时间最大时间',
            'activity-payment-title.*' => '电子票名称',
            'activity-payment-price.*' => '金额',
            'activity-payment-point.*' => '积分',
            'activity-payment-limit.*' => '名额限制',
            'refund_term' => '退款期限',
            'member_limit' => '人数限制',
            'delay_sign' => '延迟签到时间',
            'point_join' => '报名奖励积分',
            'point_sign' => '签到奖励积分',
            'point_rewards' => '教练奖励积分总额上限',
            'point_rewards_limit' => '教练奖励积分期限',
            'send_message' => '报名成功短信通知',
            'package_get_address' => '参赛包领取地址',
            'package_get_time' => '参赛包领取时间',
            'item.*.rate' => '商品折扣',
            'item.*.price' => '商品销售价',

        ];

        $input = request()->all();
        $activity_payment = [
            'activity-payment-title.*' => 'required|alpha_dash',
            'activity-payment-price.*' => 'integer|filled|min:0',
            'activity-payment-point.*' => 'integer|filled|min:0',
            'activity-payment-limit.*' => 'integer|filled|min:0',
        ];
        if ($fee_type == 'CHARGING') {
            $rules = array_merge($rules, $activity_payment);
        }

        if (isset($input['refund_term']) && $input['refund_term']) {
            $rules['refund_term'] = 'integer';
        }

        if (1 == $input['send_message']) {
            $rules['package_get_address'] = 'required';
            $rules['package_get_time'] = 'required';
        }

        $validator = Validator::make($input, $rules, $message, $attributes);
        $validator->sometimes('entry_end_at', 'before:starts_at', function ($input) {
            return $input['entry_end_at'] > $input['starts_at'];
        });

        $validator->sometimes('delay_sign', 'integer', function ($input) {
            return $input['delay_sign'] || $input['delay_sign'] > 0;
        });

        $validator->sometimes('member_limit', 'integer', function ($input) {
            return $input['member_limit'] || $input['member_limit'] > 0;
        });

        $validator->sometimes('point_join', 'integer', function ($input) {
            return $input['point_join'] || $input['point_join'] > 0;
        });

        $validator->sometimes('point_sign', 'integer', function ($input) {
            return $input['point_sign'] || $input['point_sign'] > 0;
        });

        $validator->sometimes('point_rewards', 'integer', function ($input) {
            return $input['point_rewards'] || $input['point_rewards'] > 0;
        });

        $validator->sometimes('point_rewards_limit', 'integer', function ($input) {
            return $input['point_rewards_limit'] || $input['point_rewards_limit'] > 0;
        });

        $validator->sometimes('package_get_time', 'before:starts_at', function ($input) {
            return $input['send_message'] == 1 && $input['package_get_time'] > $input['starts_at'];
        });

        $validator->sometimes(['item.*.rate', 'item.*.price'], 'required', function ($input) {
            return isset($input['item']) AND count($input['item']) > 0;
        });

        return $validator;
    }

    protected function createPayment($activity, $ids, $title, $price, $point, $limit)
    {
        if (count($ids) == 0) {
            return false;
        }

        foreach ($ids as $key => $id) {
            if (empty($id)) {
                continue;
            }

            if (empty($price[$key]) && !empty($point[$key])) {
                $type = 0;
                $price_item = 0;
            } elseif (!empty($price[$key]) && empty($point[$key])) {
                $type = 1;
                $price_item = $price[$key];
            } elseif (!empty($price[$key]) && !empty($point[$key])) {
                $type = 2;
                $price_item = $price[$key];
            } else {
                $type = 5;
                $price_item = 0;
            }

            $is_limit = $limit[$key] > 0 ? 1 : 0;
            $input = [
                'type' => $type,
                'point' => $point[$key],
                'price' => $price_item,
                'title' => $title[$key],
                'limit' => $limit[$key],
                'is_limit' => $is_limit,
            ];

            $activity->payments()->create($input);
        }

        return true;
    }

    protected function updatePayment($activity, $ids, $title, $price, $point, $limit, $status)
    {
        if (count($ids) == 0) {
            return;
        }

        foreach ($ids as $key => $id) {
            if (empty($price[$key]) && !empty($point[$key])) {
                $type = 0;
                $price_item = 0;
            } elseif (!empty($price[$key]) && empty($point[$key])) {
                $type = 1;
                $price_item = $price[$key] * 100;
            } elseif (!empty($price[$key]) && !empty($point[$key])) {
                $type = 2;
                $price_item = $price[$key] * 100;
            } else {
                $type = 5;
                $price_item = 0;
            }

            $is_limit = $limit[$key] > 0 ? 1 : 0;
            $input = [
                'type' => $type,
                'point' => $point[$key],
                'price' => $price_item,
                'title' => $title[$key],
                'limit' => $limit[$key],
                'is_limit' => $is_limit,
                'status' => $status[$key],
            ];

            $activity->payments()->where('id', $id)->update($input);
        }
    }

    protected function storeCoach($activity, $userIds)
    {
        $coachIds = $activity->members()->where('role', 'coach')->pluck('user_id')->toArray();
        foreach ($coachIds as $id) {
            if (in_array($id, $userIds)) {
                $input['user_id'] = $id;
                $input['role'] = 'coach';
                $activity->members()->where('user_id', $id)->where('role', 'coach')->update($input);
            } else {
                $activity->members()->where('user_id', $id)->where('role', 'coach')->delete();
            }
        }

        foreach ($userIds as $userId) {
            if (!in_array($userId, $coachIds)) {
                $order_no = build_order_no('AC');
                $input['user_id'] = $userId;
                $input['role'] = 'coach';
                $input['order_no'] = $order_no;
                $input['pay_status'] = 1;
                $activity->members()->create($input);
            }
        }
    }

    protected function storePoint($activity)
    {
        // 报名奖励积分
        if (!empty($point_join = request('point_join')) AND is_numeric($point_join)) {
            $actPoint = $activity->points()->where('type', 'act_join')->first();
            if ($actPoint) {
                $actPoint->update(['value' => $point_join]);
            } else {
                $activity->points()->create([
                    'type' => 'act_join',
                    'value' => $point_join,
                ]);
            }
        } else {
            $actPoint = $activity->points()->where('type', 'act_join')->first();
            if ($actPoint) {
                $actPoint->delete();
            }
        }

        // 签到奖励积分
        if (!empty($point_sign = request('point_sign')) AND is_numeric($point_sign)) {
            $actPoint = $activity->points()->where('type', 'act_sign')->first();
            if ($actPoint) {
                $actPoint->update(['value' => $point_sign]);
            } else {
                $activity->points()->create([
                    'type' => 'act_sign',
                    'value' => $point_sign,
                ]);
            }
        } else {
            $actPoint = $activity->points()->where('type', 'act_sign')->first();
            if ($actPoint) {
                $actPoint->delete();
            }
        }

        // 教练奖励积分
        if (!empty($point_rewards = request('point_rewards')) AND is_numeric($point_rewards)) {
            $point_rewards_limit = request('point_rewards_limit');

            $point_rewards_limit = (!empty($point_rewards_limit) AND is_numeric($point_rewards_limit)) ? $point_rewards_limit : null;

            $actPoint = $activity->points()->where('type', 'coach_rewards')->first();
            if ($actPoint) {
                $actPoint->update(['value' => $point_rewards, 'limit' => $point_rewards_limit]);
            } else {
                $activity->points()->create([
                    'type' => 'coach_rewards',
                    'value' => $point_rewards,
                    'limit' => $point_rewards_limit,
                ]);
            }
        } else {
            $actPoint = $activity->points()->where('type', 'coach_rewards')->first();
            if ($actPoint) {
                $actPoint->delete();
            }
        }
    }

    protected function jointWhereTime($columnTime, $starts_at, $ends_at)
    {
        $where = [];
        if (!empty($ends_at)) {
            $where[] = [$columnTime, '<=', $ends_at];
        }
        if (!empty($starts_at)) {
            $where[] = [$columnTime, '>=', $starts_at];
        }

        return $where;
    }

    public function rewards($id)
    {
        $activity = $this->activity->find($id);
        $members = $activity->members()->where('role', 'user')->where('status', 2)->get();
        $members = $members->filter(function ($member) use ($id) {
            if ($member->rewarded == 1 AND $point = $this->point->findWhere([
                    'item_type' => Activity::class,
                    'item_id' => $id,
                    'user_id' => $member->user_id,
                ])->first()
            ) {
                $member->point_status = $point->status;
                $member->point_value = $point->value;

                return true;
            }

            return false;
        });

        return Admin::content(function (Content $content) use ($activity, $members) {
            $content->description('活动奖励审核');

            $content->breadcrumb(
                ['text' => '活动列表', 'no-pjax' => 1, 'url' => 'activity/activity-list'],
                ['text' => '奖励审核', 'left-menu-active' => '活动列表']
            );

            $view = view("activity::activity.activityRewards", compact('activity', 'members'))->render();
            $content->row($view);
        });
    }

    public function rewardsStore($id)
    {
        $ids = request('ids') ?: [];
        $activity = $this->activity->find($id);
        $members = $activity->members()->where('role', 'user')->get();
        $members = $members->filter(function ($member) use ($id, $ids) {
            if ($member->rewarded == 1 AND $point = $this->point->findWhere([
                    'item_type' => Activity::class,
                    'item_id' => $id,
                    'user_id' => $member->user_id,
                ])->first()
            ) {
                if (in_array($member->id, $ids)) {
                    $point->status = 1;
                    $user = $member->user;
                    $point_total = $this->point->getSumPointValid($user->id);
                    $user->notify(new Rewards([
                        'point' => $point,
                        'point_total' => $point_total,
                    ]));
                } else {
                    $point->status = 0;
                }
                $point->save();
                $member->point_status = $point->status;
                $member->point_value = $point->value;

                return true;
            }

            return false;
        });

        return response()->json(['status' => true]);
    }

    public function filterFreeDiscount()
    {
        $discount = Discount::where('type', 2)
            ->where('status', 1)
            ->where('ends_at', '>', Carbon::now())
            ->count();
        if ($discount > 0) {
            return $this->ajaxJson(true);
        }

        return $this->ajaxJson(false, [], 304, '当前暂无可用活动通行证优惠，请先创建');
    }
}
