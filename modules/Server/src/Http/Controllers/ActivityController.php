<?php

namespace GuoJiangClub\Activity\Server\Http\Controllers;

use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Models\City;
use GuoJiangClub\Activity\Core\Models\Like;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use GuoJiangClub\Activity\Server\Services\ActivityService;
use GuoJiangClub\Activity\Server\Transformers\ActivityTransformer;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use GuoJiangClub\Activity\Server\Services\MiniProgramService;
use iBrand\Component\Point\Repository\PointRepository;
use Storage;

class ActivityController extends Controller
{
    protected $activityRepository;
    protected $activityService;
    protected $pointRepository;
    protected $member;
    protected $payment;
    protected $miniProgram;

    public function __construct(ActivityRepository $activityRepository,
                                ActivityService $activityService,
                                PointRepository $pointRepository,
                                MemberRepository $memberRepository,
                                PaymentRepository $paymentRepository,
                                MiniProgramService $miniProgramService)
    {
        $this->activityRepository = $activityRepository;
        $this->activityService = $activityService;
        $this->pointRepository = $pointRepository;
        $this->member = $memberRepository;
        $this->payment = $paymentRepository;
        $this->miniProgram = $miniProgramService;
    }

    public function index($id)
    {
        $limit = request('limit') ? request('limit') : 15;
        if ($id != 'all') {
            $city = City::find($id);
            if (!$city) {
                return $this->api([], false, 500, '城市不存在.');
            }
        }

        $meta = [];
        $query = Activity::where('status', '<>', 0);
        if ($id != 'all') {
            $meta = ['city' => $city->name];
            $query = $query->where('city_id', $id);
        }

        if (request('category_id') != 'all') {
            $query = $query->where('category_id', request('category_id'));
        }

        $activities = $query->with('payments')->orderBy('published_at', 'desc')->paginate($limit);
        if (count($activities) > 0) {
            foreach ($activities as &$activity) {
                unset($activity->content);
            }
        }

        return $this->response()->paginator($activities, new ActivityTransformer())->setMeta($meta);
    }

    public function show($id)
    {
        $activity = Activity::with(['payments' => function ($query) {
            $query->where('status', 1);
        }])->find($id);
        if ($activity) {
            return $this->response()->item($activity, new ActivityTransformer());
        }

        return $this->api([], false, 500, '活动不存在.');
    }

    public function myActivities()
    {
        $limit = request('limit') ?: 15;
        $user = request()->user();
        $activityIds = Member::where('user_id', $user->id)
            ->where('role', 'user')
            ->pluck('activity_id')->toArray();
        if ($canceled = request('canceled') AND $canceled == 1) {
            $activityIdsCanceled = Member::where('user_id', $user->id)
                ->where('role', 'user')
                ->where('status', 3)
                ->pluck('activity_id')->toArray();

            $activities = Activity::whereIn('id', $activityIdsCanceled)->where('status', '<>', 0);
        } else {
            if (!$status = request('status') OR !is_numeric($status) OR $status == 0) {
                $activities = Activity::whereIn('id', $activityIds)->where('status', '<>', 0);
            } elseif ($status == 1) {
                $activities = Activity::whereIn('id', $activityIds)->whereIn('status', [1, 4]);
            } else {
                $activities = Activity::whereIn('id', $activityIds)->where('status', $status);
            }
        }
        $activities = $activities->orderBy('published_at', 'desc')->paginate($limit);

        return $this->response()->paginator($activities, new ActivityTransformer());
    }

    public function myActivity($id)
    {
        if (!$activity = Activity::find($id)) {
            return $this->api([], false, 500, '活动不存在.');
        }
        $user = request()->user();

        if ($member = $this->activityService->getMember($activity, $user->id)) {
            $point = $this->pointRepository->getSumPointValid($user->id);

            $formData = $this->activityService->getFormData($member);

            return $this->response()->item($activity, new ActivityTransformer())->setMeta([
                'point' => $point,
                'pointUsed' => $member->point,
                'activityOrder' => $member,
                'formData' => $formData
            ]);
        } elseif ($member = $activity->members()->where('user_id', $user->id)->where('role', 'user')->orderBy('created_at', 'desc')->first()) {

            $formData = $this->activityService->getFormData($member);
            return $this->response()->item($activity, new ActivityTransformer())->setMeta(['activityOrder' => $member, 'formData' => $formData]);
        }

        return $this->api([], false, 500, '未报名此活动.');
    }

    public function myCollection()
    {
        $limit = request('limit') ?: 15;
        $user = request()->user();
        $like = Like::where('user_id', $user->id)->where('favoriteable_type', 'activity')->pluck('favoriteable_id')->toArray();
        $activities = Activity::whereIn('id', $like)->where('status', '<>', 0)->orderBy('published_at', 'desc')->paginate($limit);

        return $this->response()->paginator($activities, new ActivityTransformer());
    }

    public function coachActList()
    {
        $limit = request('limit') ?: 15;
        $user = request()->user();
        if (!$this->activityService->isCoach($user)) {
            return $this->api([], false, 500, '无权进行操作.');
        }
        $activityIds = Member::where('user_id', $user->id)
            ->where('role', 'coach')
            ->pluck('activity_id')->toArray();
        if (!$status = request('status') OR !is_numeric($status) OR $status == 0) {
            $activities = Activity::whereIn('id', $activityIds)->where('status', '<>', 0);
        } elseif ($status == 1) {
            $activities = Activity::whereIn('id', $activityIds)->whereIn('status', [1, 4]);
        } else {
            $activities = Activity::whereIn('id', $activityIds)->where('status', $status);
        }
        $activities = $activities->orderBy('published_at', 'desc')->paginate($limit);

        return $this->response()->paginator($activities, new ActivityTransformer());
    }

    public function coachAct($id)
    {
        if (!$activity = Activity::with('payments')->find($id)) {
            return $this->api([], false, 500, '活动不存在.');
        }
        $user = request()->user();
        if (!$this->activityService->isCoach($user) OR !$this->activityService->belongsToCoach($activity, $user)) {
            return $this->api([], false, 500, '无权进行操作.');
        }

        if (request('type') == 'mini_program') {
            $code = $this->activityService->getActCode($activity->id, 'mini_program');
            $url = $this->miniProgram->createMiniQrcode('pages/userSign/main', 260, $activity->id . ',' . $code);
            if ($url) {
                return $this->response()->item($activity, new ActivityTransformer())->setMeta(['img_src' => env('APP_URL') . Storage::url($url)]);
            } else {
                return $this->api([], false, 500, '生成小程序码失败');
            }
        } else {
            return $this->response()->item($activity, new ActivityTransformer())->setMeta(['img_src' => 'data:image/png;base64,' . $this->activityService->getActQrCode($activity->id)]);
        }
    }

    public function formFields($id)
    {
        if (!$id) {
            return $this->api([], false, 500, '参数错误');
        }

        $activity = Activity::find($id);
        if (!$activity) {
            return $this->api([], false, 500, '活动不存在');
        }

        $user = request()->user();
        $mobile = $user->mobile;
        $data = [];
        if (isset($activity->form) && $activity->form) {
            $data = json_decode($activity->form->fields, true);
        }

        if (is_array($data)) {
            foreach ($data as &$v) {
                if ($v['name'] == 'mobile') {
                    $v['value'] = $mobile;
                } else {
                    $v['value'] = '';
                }
            }
        }

        if (isset($activity->statement) && $activity->statement) {
            $statement = ['status' => 1, 'is_necessary' => 1, 'type' => 'statement', 'index' => 0, 'name' => 'statement', 'title' => $activity->statement->title, 'value' => $activity->statement->statement];
            array_push($data, $statement);
        }

        return $this->api($data, true, 200, '');
    }

    public function getOrderInfo($order_no)
    {
        if (!$order_no) {
            return $this->api([], false, 500, '订单不存在');
        }

        $order = $this->member->findWhere(['order_no' => $order_no])->first();
        if (!$order) {
            return $this->api([], false, 500, '订单不存在');
        }

        $activity = $this->activityRepository->find($order->activity_id);
        if (!$activity) {
            return $this->api([], false, 500, '订单不存在');
        }

        $payment = $this->payment->find($order->payment_id);

        $point = app(PointRepository::class)->getSumPointValid($order->user_id);

        return $this->api([
            'activity' => $activity,
            'payment' => $payment,
            'point' => $point,
            'activityOrder' => $order
        ]);
    }

    public function detail($id)
    {
        $activity = Activity::find($id);
        return view('activity-server::detail', compact('activity'));
    }
}
