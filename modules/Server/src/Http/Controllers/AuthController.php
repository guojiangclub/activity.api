<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/9
 * Time: 12:10
 */

namespace GuojiangClub\Activity\Server\Http\Controllers;

use ElementVip\Component\User\Models\UserBind;

class AuthController extends Controller
{
    public function getOpenId()
    {
        $redirect = request('redirect_url') ? urlencode(request('redirect_url')) : settings('mobile_activity_domain');

        return redirect(env('WECHAT_API_URL') . 'oauth?appid=' . settings('activity_app_id') . '&scope=snsapi_base&redirect=' . $redirect);
    }

    public function bindActivityWx()
    {
        $userId = request()->user()->id;
        $openId = request('open_id');
        $type='wechat';

        if (empty($openId)) {
            return $this->api([], false, 500, '参数为空.');
        }

        $userBind = UserBind::byOpenIdAndType($openId, $type)->first();

        if (empty($userBind)) {
            UserBind::create(['open_id' => $openId, 'type' => $type, 'user_id' => $userId,'app_id'=>settings('activity_app_id')]);
        } else {
            $userBind->user_id = $userId;
            $userBind->app_id = settings('activity_app_id');
            $userBind->save();
        }

        return $this->api([], true, 200, '绑定成功.');
    }
}