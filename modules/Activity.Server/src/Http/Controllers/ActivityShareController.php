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

use EasyWeChat;
use GuoJiangClub\Activity\Core\Models\Activity;
use iBrand\Miniprogram\Poster\MiniProgramShareImg;
use Illuminate\Http\Request;
use Storage;
use Validator;

class ActivityShareController extends Controller
{
    protected $day = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

    public function index(Request $request)
    {
        $input = $request->except('_token');
        $rules = [
            'activity_id' => 'required',
            'page' => 'required',
        ];
        $message = [
            'required' => ':attribute 不能为空',
        ];
        $attributes = [
            'activity_id' => '活动id',
            'page' => '参数page',
        ];
        $validator = Validator::make($input, $rules, $message, $attributes);
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return $this->api([], false, 500, $show_warning);
        }

        $activity = Activity::where('id', $input['activity_id'])->first();
        if (!$activity) {
            return $this->failed('活动不存在');
        }

        config(['filesystems.disks.public.url' => env('APP_URL').'/storage']);
        $path = $this->getQrCode($input['activity_id'], $input['page']);
        if (!$path) {
            return $this->api([], false, 500, '生成小程序码失败');
        }

        $route = url('api/activity/share/template?activity_id='.$input['activity_id']);
        $img = MiniProgramShareImg::generateShareImage($route);
        if (!$img) {
            return $this->api([], false, 500, '生成分享图片失败');
        }

        return $this->api(['url' => $img['url']]);
    }

    public function template()
    {
        $activity = Activity::where('id', request('activity_id'))->first();
        $img_name = request('activity_id').'_activity_'.'mini_qrcode.jpg';
        $savePath = 'activity/qrcode/'.$img_name;
        $qrCodeUrl = Storage::disk('public')->url($savePath);

        $paymentText = '';
        if (isset($activity->payments) && $activity->payments->count() > 0) {
            $payment = $activity->payments->first();
            switch ($payment->type) {
                case 0:
                    $paymentText = $payment->point.'积分';
                    break;
                case 1:
                    $paymentText = '￥'.$payment->price;
                    break;
                case 2:
                    $paymentText = $payment->point.'积分'.' + '.'￥'.$payment->price;
                    break;
                case 3:
                    $paymentText = '活动通行证';
                    break;
                case 4:
                    $paymentText = '￥'.$payment->price;
                    break;
                case 5:
                    $paymentText = '免费票';
                    break;
            }
        }

        $start = date('Y-m-d', strtotime($activity->starts_at));
        $start_time = date('H:i', strtotime($activity->starts_at));
        $start_week = date('w', strtotime($activity->starts_at));
        $start_week = $this->day[$start_week];
        $end = date('Y-m-d', strtotime($activity->ends_at));
        $end_time = date('H:i', strtotime($activity->ends_at));
        $end_week = date('w', strtotime($activity->ends_at));
        $end_week = $this->day[$end_week];

        return view('activity-server::share.template', compact('activity', 'qrCodeUrl', 'paymentText', 'start', 'end', 'start_time', 'end_time', 'start_week', 'end_week'));
    }

    public function getQrCode($scene, $page, $width = 430)
    {
        $img_name = $scene.'_activity_'.'mini_qrcode.jpg';
        $savePath = 'activity/qrcode/'.$img_name;
        if (Storage::disk('public')->exists($savePath)) {
            return $savePath;
        }

        $config = [
            'app_id' => settings('activity_mini_program_app_id'),
            'secret' => settings('activity_mini_program_secret'),
        ];
        $miniProgram = EasyWeChat\Factory::miniProgram($config);
        $response = $miniProgram->app_code->getUnlimit($scene, [
            'page' => $page,
            'width' => $width,
        ]);

        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode || empty($contents) || '{' === $contents[0]) {
            return false;
        }

        $result = Storage::disk('public')->put($savePath, $response);
        if ($result) {
            return $savePath;
        }

        return false;
    }
}
