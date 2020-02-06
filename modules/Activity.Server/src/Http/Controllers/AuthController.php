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

use GuoJiangClub\Activity\Core\Models\User;
use iBrand\Component\User\Models\UserBind;
use iBrand\Component\User\Repository\UserRepository;
use iBrand\Component\User\Repository\UserBindRepository;
use iBrand\Sms\Facade as Sms;

class AuthController extends Controller
{
	protected $userRepository;
	protected $userBindRepository;

	public function __construct(UserRepository $userRepository, UserBindRepository $userBindRepository)
	{
		$this->userRepository     = $userRepository;
		$this->userBindRepository = $userBindRepository;
	}

	/**
	 * @return \Illuminate\Http\Response|mixed
	 *
	 * @throws \Exception
	 */
	public function smsLogin()
	{
		$mobile = request('mobile');
		$code   = request('code');

		if (!Sms::checkCode($mobile, $code)) {
			return $this->failed('验证码错误');
		}

		$is_new = false;

		if (!$user = $this->userRepository->getUserByCredentials(['mobile' => $mobile])) {
			$data   = ['mobile' => $mobile];
			$user   = $this->userRepository->create($data);
			$is_new = true;
		}

		if (User::STATUS_FORBIDDEN == $user->status) {
			return $this->failed('您的账号已被禁用，联系网站管理员或客服！');
		}

		$token = $user->createToken($mobile)->accessToken;

		return $this->success(['token_type' => 'Bearer', 'access_token' => $token, 'is_new_user' => $is_new]);
	}

	public function getOpenId()
	{
		$redirect = request('redirect_url') ? urlencode(request('redirect_url')) : settings('mobile_activity_domain');

		return redirect(env('WECHAT_API_URL') . 'oauth?appid=' . settings('activity_app_id') . '&scope=snsapi_base&redirect=' . $redirect);
	}

	public function bindActivityWx()
	{
		$userId = request()->user()->id;
		$openId = request('open_id');
		$type   = 'wechat';

		if (empty($openId)) {
			return $this->api([], false, 500, '参数为空.');
		}

		$userBind = UserBind::byOpenIdAndType($openId, $type)->first();

		if (empty($userBind)) {
			UserBind::create(['open_id' => $openId, 'type' => $type, 'user_id' => $userId, 'app_id' => settings('activity_app_id')]);
		} else {
			$userBind->user_id = $userId;
			$userBind->app_id  = settings('activity_app_id');
			$userBind->save();
		}

		return $this->api([], true, 200, '绑定成功.');
	}
}
