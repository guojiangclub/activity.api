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

use DB;
use GuoJiangClub\Component\User\Models\Role;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Models\City;
use GuoJiangClub\Activity\Core\Models\Statement;
use GuoJiangClub\Activity\Core\Repository\ActivityRepository;
use GuoJiangClub\Activity\Core\Repository\MemberRepository;
use GuoJiangClub\Activity\Core\Repository\PaymentRepository;
use iBrand\Component\Vip\Repositories\VipMemberRepository;
use Illuminate\Http\Request;
use Storage;
use Validator;

class PublishController extends Controller
{
    protected $vipMemberRepository;
    protected $activityRepository;
    protected $paymentRepository;
    protected $memberRepository;

    public function __construct(VipMemberRepository $vipMemberRepository, ActivityRepository $activityRepository, PaymentRepository $paymentRepository, MemberRepository $memberRepository)
    {
        $this->vipMemberRepository = $vipMemberRepository;
        $this->activityRepository = $activityRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
    }

    public function init()
    {
        $user = request()->user();
        $vip = $this->vipMemberRepository->getDefaultByUserId($user->id);
        $can_publish = 0;
        if ($vip && $vip->plan->level >= 2) {
            $can_publish = 1;
        }

        $cities = City::all();
        $statement = Statement::find(settings('activity_publish_statement_id'));

        return $this->success([
            'can_publish' => $can_publish,
            'city_list' => $cities,
            'form_id' => settings('activity_publish_form_id'),
            'statement' => $statement,
            'img_list' => settings('activity_publish_img_list'),
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->except('_token', 'file');
        $rules = [
            'title' => 'required',
            'img' => 'required',
            'img_list' => 'required',
            'content' => 'required',
            'starts_at' => 'required|date ',
            'ends_at' => 'required|date|after:starts_at',
            'entry_end_at' => 'required|date',
            'city_id' => 'required|integer',
            'address' => 'required',
            'address_point' => 'required',
            'member_limit' => 'required|integer',
            'form_id' => 'required|integer',
            'statement_id' => 'required|integer',
            'payments' => 'required|array',
            'payments.*.title' => 'required',
            'payments.*.price' => 'required|numeric|min:0',
            'payments.*.point' => 'required|integer|min:0',
            'payments.*.limit' => 'required|integer|min:0',
        ];
        $message = [
            'required' => ':attribute 不能为空',
            'integer' => ':attribute 只能为整数',
            'numeric' => ':attribute 只能为数字',
            'min' => ':attribute 最小只能为0',
            'ends_at.after' => ':attribute 不能早于 活动开始时间',
        ];
        $attributes = [
            'title' => '活动名称',
            'img' => '活动图片',
            'img_list' => '列表图片',
            'content' => '活动详情',
            'starts_at' => '活动开始时间',
            'ends_at' => '活动结束时间',
            'entry_end_at' => '报名截止时间',
            'city_id' => '活动城市',
            'address' => '活动地址',
            'address_point' => '活动地址',
            'member_limit' => '报名人数',
            'form_id' => '活动报名表单',
            'statement_id' => '活动免责声明',
            'payments' => '报名费用',
            'payments.*.title' => '电子票名称',
            'payments.*.price' => '电子票金额',
            'payments.*.point' => '电子票积分',
            'payments.*.limit' => '电子票数量限制',
        ];
        $validator = Validator::make($input, $rules, $message, $attributes);
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return $this->failed($show_warning);
        }

        if (strtotime($input['entry_end_at']) > strtotime($input['starts_at'])) {
            return $this->failed('报名截止时间 不能晚于 活动开始时间');
        }

        $user = request()->user();
        $vip = $this->vipMemberRepository->getDefaultByUserId($user->id);
        if (!$vip && $vip->plan->level < 2) {
            return $this->failed('您还不是SVIP，不能发布活动');
        }

        $input['user_id'] = $user->id;
        $input['fee_type'] = Activity::FEE_TYPE_CHARGING;
        $input['address_name'] = $input['address'];
        $payments = $input['payments'];
        unset($input['payments']);

        try {
            DB::beginTransaction();

            $coach = Role::where('name', 'coach')->first();
            if (!$coach) {
                $coach = Role::create([
                    'name' => 'coach',
                    'display_name' => '教练',
                ]);
            }

            $coachUser = DB::table('el_role_user')->where('role_id', $coach->id)->where('user_id', $user->id)->first();
            if (!$coachUser) {
                DB::table('el_role_user')->insert(['role_id' => $coach->id, 'user_id' => $user->id]);
            }

            $activity = $this->activityRepository->create($input);

            $payment = $this->addActivityPayments($activity, $payments);
            if (!$payment) {
                throw new \Exception('报名费用错误');
            }

            $member = $this->memberRepository->findWhere(['activity_id' => $activity->id, 'user_id' => $user->id, 'role' => 'coach'])->first();
            if (!$member) {
                $this->memberRepository->create([
                    'activity_id' => $activity->id,
                    'user_id' => $user->id,
                    'role' => 'coach',
                    'order_no' => build_order_no('AC'),
                    'status' => 1,
                    'pay_status' => 1,
                ]);
            }

            DB::commit();

            return $this->success($activity);
        } catch (\Exception $exception) {
            DB::rollBack();

            \Log::info($exception->getMessage());
            \Log::info($exception->getTraceAsString());

            return $this->failed('活动发布失败');
        }
    }

    public function addActivityPayments($activity, $payments)
    {
        if (empty($payments)) {
            return false;
        }

        foreach ($payments as $payment) {
            if ($payment['price'] <= 0 && $payment['point'] > 0) {
                $type = 0;
            } elseif ($payment['price'] > 0 && $payment['point'] <= 0) {
                $type = 1;
            } elseif ($payment['price'] > 0 && $payment['point'] > 0) {
                $type = 2;
            } else {
                $type = 5;
            }

            $is_limit = $payment['limit'] > 0 ? 1 : 0;
            $data = [
                'activity_id' => $activity->id,
                'type' => $type,
                'point' => $payment['point'],
                'price' => $payment['price'],
                'title' => $payment['title'],
                'limit' => $payment['limit'],
                'is_limit' => $is_limit,
                'status' => 1,
                'discount_id' => 0,
            ];

            $this->paymentRepository->create($data);
        }

        return true;
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('image')) {
            return $this->failed('请上传图片');
        }

        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $this->failed('只允许上传jpg,jpeg,png,gif格式的图片');
        }

        $name = md5(uniqid()).'.'.$ext;
        $path = $image->storeAs(
            'activity/upload/image', $name, 'public'
        );

        if (Storage::disk('public')->exists($path)) {
            $this->compress(config('filesystems.disks.public.root').'/'.$path, $ext);
        }

        return $this->success(['url' => Storage::disk('public')->url($path)]);
    }

    public function compress($file, $ext)
    {
        list($width, $height, $type) = getimagesize($file);
        $new_width = $width * 1;
        $new_height = $height * 1;

        $resource = imagecreatetruecolor($new_width, $new_height);
        switch ($ext) {
            case 'jpeg':
                $image = imagecreatefromjpeg($file);
                break;
            case 'jpg':
                $image = imagecreatefromjpeg($file);
                break;
            case 'png':
                $image = imagecreatefrompng($file);
                break;
        }

        imagecopyresampled($resource, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        switch ($ext) {
            case 'jpeg':
                imagejpeg($resource, $file, config('ibrand.miniprogram-poster.quality', 9));
                break;
            case 'jpg':
                imagejpeg($resource, $file, config('ibrand.miniprogram-poster.quality', 9));
                break;
            case 'png':
                imagepng($resource, $file, config('ibrand.miniprogram-poster.quality', 9));
                break;
        }

        imagedestroy($resource);
    }
}
