<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Server\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Models\Discount\Coupon;
use GuoJiangClub\Activity\Core\Models\Like;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Models\Payment;
use GuoJiangClub\Activity\Core\Models\User;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use GuoJiangClub\Activity\Core\Repository\CouponRepository;
use GuoJiangClub\Activity\Core\Repository\DiscountRepository;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Server\Services\ActivityService;
use GuoJiangClub\Activity\Server\Transformers\ActivityTransformer;
use iBrand\Component\Point\Repository\PointRepository;

class MemberController extends Controller
{
    protected $activityRepository;
    protected $activityService;
    protected $pointRepository;
    protected $discountRepository;
    protected $couponRepository;
    protected $member;

    public function __construct(ActivityRepository $activityRepository,
                                ActivityService $activityService,
                                PointRepository $pointRepository,
                                DiscountRepository $discountRepository,
                                CouponRepository $couponRepository,
                                MemberRepository $memberRepository)
    {
        $this->activityRepository = $activityRepository;
        $this->activityService = $activityService;
        $this->pointRepository = $pointRepository;
        $this->discountRepository = $discountRepository;
        $this->couponRepository = $couponRepository;
        $this->member = $memberRepository;
    }

    public function isMember($id)
    {
        $activity = $this->activityRepository->with(['payments' => function ($query) {
            $query->where('status', 1);
        }])->find($id);
        if (!$activity) {
            return $this->api([], false, 500, '活动不存在.');
        }

        $user = request()->user();
        $pay_status = 0;
        $member_status = 0;
        $liked = $this->activityService->isLiked($user->id, $activity->id);

        $activityOrder = $this->member->findWhere(['user_id' => $user->id, 'activity_id' => $id, ['status', '<>', 3]])->first();
        if ($activityOrder) {
            $payment = Payment::find($activityOrder->payment_id);
            $pay_status = $activityOrder->pay_status;
            $member_status = $activityOrder->status;
            if ($payment and (1 == $payment->type or 2 == $payment->type)) {
                $activityOrder = $this->member->findWhere(['status' => 0, 'pay_status' => 0, 'activity_id' => $id, 'user_id' => $user->id])->first();

                if ($activityOrder) {
                    $pay_status = $activityOrder->pay_status;
                    $member_status = $activityOrder->status;
                }
            }
        }

        return $this->api([
            'member_status' => $member_status,
            'pay_status' => $pay_status,
            'liked' => $liked,
            'order' => $activityOrder,
            'activity' => $activity,
        ]);
    }

    public function isCoach()
    {
        $user = request()->user();

        return $this->api([
            'isCoach' => $this->activityService->isCoach($user) ? 1 : 0,
        ]);
    }

    public function memberList($id)
    {
        if (!$activity = Activity::find($id)) {
            return $this->api([], false, 500, '活动不存在.');
        }
        $user = request()->user();
        if (!$this->activityService->isCoach($user) or !$this->activityService->belongsToCoach($activity, $user)) {
            return $this->api([], false, 500, '无权进行操作.');
        }

        return $this->api([
            'signed' => $activity->members()->where('role', 'user')->where('status', 2)->get(),
            'unsigned' => $activity->members()->where('role', 'user')->where('status', 1)->get(),
        ]);
    }

    public function sign($id)
    {
        if (!$activity = Activity::find($id)) {
            return $this->api([], false, 500, '活动不存在.');
        }

        $user = request()->user();
        if (!$this->activityService->canSign($activity, $user)) {
            return $this->api([], false, 500, '您已签到.');
        }

        $code = request('code');
        $type = request('type') ? request('type') : 'wechat';
        if (empty($code) or $this->activityService->getActCode($activity->id, $type) != $code) {
            return $this->api([], false, 500, '签到验证失败.');
        }
        $member = $this->activityService->getMember($activity, $user->id);
        $member->signed_at = Carbon::now();
        $member->status = 2;
        $member->save();

        event('on.member.activity.status.change', [$user->id, $activity, 'act_sign']);

        $coupon = null;
        if ($discount = $this->discountRepository->getDiscountByCode(settings('activity_coupon_code_sign'), 1) and $this->couponRepository->canGetCoupon($discount, $user)) {
            $coupon = Coupon::create([
                'discount_id' => $discount->id,
                'user_id' => $user->id,
            ]);
        }

        return $this->api(['coupon' => $coupon, 'user' => $user]);
    }

    public function like($id)
    {
        if (!$activity = Activity::find($id)) {
            return $this->api([], false, 500, '活动不存在.');
        }
        $user = request()->user();
        if (!$like = Like::where('user_id', $user->id)->where('favoriteable_id', $id)->where('favoriteable_type', 'activity')->first()) {
            $like = Like::create([
                'user_id' => $user->id,
                'favoriteable_id' => $id,
                'favoriteable_type' => 'activity',
            ]);

            return $this->api([
                'liked' => 1,
            ]);
        }
        Like::where('user_id', $user->id)->where('favoriteable_id', $id)->where('favoriteable_type', 'activity')->delete();

        return $this->api([
                'liked' => 0,
            ]);
    }

    public function dislike()
    {
        $user = request()->user();
        $dislikeIds = (request('dislike') and is_array(request('dislike'))) ? request('dislike') : [];
        $like = Like::where('user_id', $user->id)->whereIn('favoriteable_id', $dislikeIds)->where('favoriteable_type', 'activity');
        $like->delete();

        return $this->api();
    }

    public function rewards($id)
    {
        if (!$activity = Activity::find($id)) {
            return $this->api([], false, 500, '活动不存在.');
        }
        $user = request()->user();
        if (!$this->activityService->isCoach($user) or !$this->activityService->belongsToCoach($activity, $user)) {
            return $this->api([], false, 500, '无权进行操作.');
        }
        $targetIds = request('user_ids');
        $point = request('value') * 1;
        if (empty($point)) {
            return $this->api([], false, 500, '无效数值.');
        }
        if (($point * count($targetIds)) > $activity->can_reward) {
            return $this->api([], false, 500, '奖励总额超过限制.');
        }
        foreach ($targetIds as $targetId) {
            if ($member = $this->activityService->getMember($activity, $targetId) and 0 == $member->rewarded) {
                $this->pointRepository->create([
                    'user_id' => $targetId,
                    'action' => 'activity_reward',
                    'note' => '野练奖励积分',
                    'value' => $point,
                    'valid_time' => 0,
                    'status' => 0,
                    'item_type' => Activity::class,
                    'item_id' => $activity->id,
                ]);
            }
        }

        return $this->api();
    }

    public function memberInfo($id)
    {
        $limit = request('limit') ?: 15;
        $user = request()->user();
        if (!$member = User::find($id)) {
            return $this->api([], false, 500, '不存在此会员.');
        }
        if (!$this->activityService->isCoach($user)) {
            return $this->api([], false, 500, '无权进行操作.');
        }
        $activityIds = Member::where('user_id', $member->id)
            ->where('role', 'user')
            ->where('status', '<>', 3)
            ->pluck('activity_id')->toArray();
        $activities = Activity::whereIn('id', $activityIds)->where('status', '<>', 0)->orderBy('published_at', 'desc')->paginate($limit);

        return $this->response()->paginator($activities, new ActivityTransformer())->setMeta([
            'user' => $member,
            'size' => $member->size,
        ]);
    }
}
