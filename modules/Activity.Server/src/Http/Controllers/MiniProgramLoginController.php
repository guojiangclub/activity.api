<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/8
 * Time: 13:12
 */

namespace GuoJiangClub\Activity\Server\Http\Controllers;

use GuoJiangClub\Activity\Core\Models\User;
use GuoJiangClub\Activity\Server\Overtrue\WXBizDataCrypt;
use iBrand\Component\User\Repository\UserBindRepository;
use RuntimeException;

class MiniProgramLoginController extends Controller
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';

    const CODE_URL = 'https://api.weixin.qq.com/sns/jscode2session';

    protected $bind;

    public function __construct(UserBindRepository $userBindRepository)
    {
        $this->bind = $userBindRepository;
    }

    public function login()
    {
        $app_id = settings('activity_mini_program_app_id');
        $secret = settings('activity_mini_program_secret');

        $code = request('code');
        if (empty($app_id) OR empty($secret)) {
            return $this->response()->errorBadRequest('Please configure mini_program_app_id and mini_program_secret');
        }
        if (empty($code)) return $this->api([], false, 403, '缺失code');
        $params = [
            'appid' => $app_id,
            'secret' => $secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];
        $res = $this->Curl(self::CODE_URL, self::GET, $params);

        if (!isset($res['openid'])) {
            return $this->api([], false, 403, '获取open_id失败');
        }

        $openID = $res['openid'];
        $session_key = $res['session_key'];
        $cacheKey = $openID . '_mini_program_session_key';
        \Cache::forget($cacheKey);
        \Cache::put($cacheKey, $session_key, 10);

        $unionID = null;
        if (isset($res['unionid'])) {
            $unionID = $res['unionid'];
        }

        $userBind = $this->bind->getByOpenId($openID);

        if ($userBind AND $user = \GuoJiangClub\Activity\Core\Models\User::where('id', $userBind->user_id)->first()) {

            $token = $user->createToken($user->mobile)->accessToken;

            return response()
                ->json(['token_type' => 'Bearer', 'access_token' => $token, 'redirect_url' => '']);
        }

        return $this->api(['open_id' => $openID], true, 200, '');

    }


    public function MiniProgramMobileLogin()
    {
        if (is_null($model = config('auth.providers.users.model'))) {
            throw new RuntimeException('Unable to determine user model from configuration.');
        }

        $app_id = settings('activity_mini_program_app_id');
        $secret = settings('activity_mini_program_secret');

        $encryptedData = request('encryptedData');
        $iv = request('iv');
        $is_new = false;

        $params = [
            'appid' => $app_id,
            'secret' => $secret,
            'js_code' => request('code'),
            'grant_type' => 'authorization_code'
        ];
        $res = $this->Curl(self::CODE_URL, self::GET, $params);
        if (!isset($res['session_key'])) {
            return $this->api([], false, 403, '获取session_key失败');
        }
        $sessionKey = $res['session_key'];


        $pc = new WXBizDataCrypt($app_id, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            $getPhoneData = json_decode($data, true);
            $mobile = $getPhoneData['purePhoneNumber'];
            if (!$user = $model::where('mobile', $mobile)->first()) {
                $user = $model::create([
                    'mobile' => $mobile
                ]);
                $is_new = true;
            }

            $token = $user->createToken($mobile)->accessToken;
            $this->bindOpenPlatform($user->id);

            return response()
                ->json(['token_type' => 'Bearer', 'access_token' => $token, 'redirect_url' => '', 'is_new_user' => $is_new]);
        } else {
            return $this->api([], false, 403, '获取手机号码失败');
        }

    }

    private function Curl($url, $method = self::GET, $params = [], $request_header = [])
    {
        $request_header = ['Content-Type' => 'application/x-www-form-urlencoded'];
        if ($method === self::GET || $method === self::DELETE) {
            $url .= (stripos($url, '?') ? '&' : '?') . http_build_query($params);
            $params = [];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        if ($method === self::POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

    private function bindUserInfo($user_id, $userWxInfo)
    {
        $userInfo = [];
        if (!$user = User::find($user_id)) return;

        if (!$user->nick_name AND isset($userWxInfo['nickName'])) {
            $userInfo['nick_name'] = $userWxInfo['nickName'];
        }
        if (!$user->sex AND isset($userWxInfo['gender'])) {
            $userInfo['sex'] = $userWxInfo['gender'] == 1 ? '男' : '女';
        }
        if (!$user->avatar AND isset($userWxInfo['avatarUrl'])) {
            $userInfo['avatar'] = $userWxInfo['avatarUrl'];
        }
        if (!$user->city AND isset($userWxInfo['city'])) {
            $userInfo['city'] = $userWxInfo['city'];
        }
        if (!$user->union_id AND isset($userWxInfo['unionID'])) {
            $userInfo['union_id'] = $userWxInfo['unionID'];
        }

        if (count($userInfo) > 0) {
            $user->fill($userInfo);
            $user->save();
        }

    }

    private function bindOpenPlatform($userId)
    {
        $openId = request('open_id');
        $type = 'miniprogram';
        $app_id = settings('activity_mini_program_app_id');

        $app_type = request('app_type');


        if (empty($openId) OR empty($type)) {
            return;
        }

        $userBind = $this->bind->getByOpenId($openId);

        if (empty($userBind)) {
            $this->bind->create(['open_id' => $openId, 'type' => $type, 'user_id' => $userId, 'app_id' => $app_id]);
        } else {
            $this->bind->bindToUser($openId,$userId);
        }

        $this->bindUserInfo($userId, request('userInfo'));
    }
}