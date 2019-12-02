<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-08-23
 * Time: 12:02
 */

namespace GuoJiangClub\Activity\Server\Http\Controllers;

use GuoJiangClub\Activity\Server\Overtrue\WXBizDataCrypt;
use GuoJiangClub\Activity\Server\Transformers\UserTransformer;
use iBrand\Component\Vip\Repositories\VipMemberRepository;
use Illuminate\Http\Request;
use Validator;
use Image;
use iBrand\Sms\Facade as Sms;
use EasyWeChat;


class UserController extends Controller
{

    private $user;
    protected $couponRepository;
    protected $vipMemberRepository;
    protected $signItemRepository;

    public function __construct(
        VipMemberRepository $vipMemberRepository
    )
    {
        $this->vipMemberRepository = $vipMemberRepository;
    }

    public function me()
    {
        $user = request()->user();

        $user->employee = false;
        $user->coach = false;
        foreach ($user->roles as $role) {
            if ($role->name == 'coach') {
                $user->coach = true;
            }
        }

        return $this->response()->item($user, new UserTransformer());
    }

    public function updateInfo()
    {
        $user = request()->user();
        $data = array_filter(request()->only(['name', 'nick_name', 'sex', 'birthday', 'city', 'education', 'qq', 'avatar', 'email', 'password']));
        $size_input = request('size') ?: [];
        $size_input = array_filter(array_only($size_input, ['upper', 'lower', 'shoes']));

        if (isset($data['name']) and ($data['name']) != $user->name AND User::where('name', $data['name'])->first()) {
            return response()->json([
                'status' => false,
                'message' => '此用户名已存在',
            ]);
        }

        if (isset($data['email']) and ($data['email']) != $user->email AND User::where('email', $data['email'])->first()) {
            return response()->json([
                'status' => false,
                'message' => '该邮箱已被使用',
            ]);
        }

        $user->fill($data);
        $user->save();
        $size = $user->size;
        if ($size) {
            $size->update($size_input);
            $size->save();
        } else {
            $user->size()->create($size_input);
        }


        return response()->json([
            'status' => true,
            'message' => "修改成功",
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        //TODO: 需要验证是否传入avatar_file 参数
        $file = $request->file('avatar_file');
        $Orientation = $request->input('Orientation');

        $destinationPath = storage_path('app/public/uploads');
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $extension = $file->getClientOriginalExtension();
        $filename = $this->generaterandomstring() . '.' . $extension;

        $image = $file->move($destinationPath, $filename);

        $img = Image::make($image);

        switch ($Orientation) {
            case 6://需要顺时针（向左）90度旋转
                $img->rotate(-90);
                break;
            case 8://需要逆时针（向右）90度旋转
                $img->rotate(90);
                break;
            case 3://需要180度旋转
                $img->rotate(180);
                break;
        }

        if (request('action_type') == 'full') {
            $img->resize(320, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->resize(320, null, function ($constraint) {
                $constraint->aspectRatio();
            })->crop(320, 320, 0, 0)->save();
        }

        if (request('action') == 'save') {
            $user = $request->user();
            $user->avatar = '/storage/uploads/' . $filename;
            $user->save();
        }

        return $this->api(['url' => url('/storage/uploads/' . $filename)]);
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response|void
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $check = \Hash::check($request->input('old_password'), $user->password);
        if (!$check) {
            return $this->api([], false, 200, '原始密码错误');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed|max:25',
        ]);

        if ($validator->fails()) {
            return $this->api([], false, 200, '密码长度少于6位或确认密码不一致');
        }

        if (!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]+$/', $request->input('password'))) {
            return $this->api([], false, 200, '密码只能是数字和字母组合');
        }

        $user = $this->user->update(['password' => $request->input('password')], $user->id);

        return $this->response()->item($user, new UserTransformer());
    }

    public function updateMobile(Request $request)
    {

        if (!Sms::checkCode(\request('mobile'), \request('code'))) {
            return $this->api(null, false, 400, '验证码错误');
        }

        $user = $request->user();

        if ($existUser = $this->user->findWhere(['mobile' => request('mobile')])->first()) {
            return $this->api(null, false, 400, '此手机号已绑定账号');
        }

        $user = $this->user->update(['mobile' => $request->input('mobile')], $user->id);

        return $this->response()->item($user, new UserTransformer());
    }


    private function generaterandomstring($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }


    /**
     * 小程序绑定手机号码
     *
     * @return \Dingo\Api\Http\Response
     */
    public function MiniProgramBindMobile()
    {
        $user = request()->user();
        $iv = request('iv');
        $encryptedData = request('encryptedData');

        $app_id = settings('mini_program_app_id');
        $secret = settings('mini_program_secret');
        $code = request('code');

        if (empty($code)) {
            return $this->api([], false, 400, '缺失code');
        }
        $params = [
            'appid' => $app_id,
            'secret' => $secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $res = $this->Curl('https://api.weixin.qq.com/sns/jscode2session', 'GET', $params);
        if (!isset($res['session_key'])) {
            return $this->api([], false, 400, '获取session_key失败');
        }

        $pc = new WXBizDataCrypt($app_id, $res['session_key']);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            $miniProgramResult = json_decode($data, true);
            \Log::info('bind mobile miniProgramResult:' . json_encode($miniProgramResult));
            $mobile = $miniProgramResult['purePhoneNumber'];
            if ($existUser = $this->user->findWhere(['mobile' => $mobile])->first()) {
                return $this->api(null, false, 400, '此手机号已绑定账号');
            }

            if ($user->mobile) {
                return $this->api(null, false, 400, '已绑定手机号码');
            }

            $user = $this->user->update(['mobile' => $mobile], $user->id);
            event('user.update.mobile', [$user]);
            event('verify_mobile', $user);

            return $this->api([], true, 200, '绑定成功');
        }

        return $this->api([], true, 400, '获取手机号码失败');
    }

    private function Curl($url, $method = 'GET', $params = [], $request_header = [])
    {
        $request_header = ['Content-Type' => 'application/x-www-form-urlencoded'];
        if ($method === 'GET' || $method === 'DELETE') {
            $url .= (stripos($url, '?') ? '&' : '?') . http_build_query($params);
            $params = [];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

    public function bindUserMiniInfo()
    {
        $type = request('app_type');
        $config = [
            'app_id' => settings('activity_mini_program_app_id'),
            'secret' => settings('activity_mini_program_secret'),
        ];
        $miniProgram = EasyWeChat\Factory::miniProgram($config);

        //1. get session key.
        $code = request('code');
        $result = $miniProgram->auth->session($code);

        if (!isset($result['session_key'])) {
            return $this->failed('获取 session_key 失败.');
        }

        $sessionKey = $result['session_key'];

        //2. get user info.
        $encryptedData = request('encryptedData');
        $iv = request('iv');

        $decryptedData = $miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptedData);

        $user = request()->user();
        $user->nick_name = $decryptedData['nickName'];
        $user->sex = $decryptedData['gender'] == 1 ? '男' : '女';
        $user->avatar = $decryptedData['avatarUrl'];
        $user->save();

        return $this->success(['user_info' => $user]);
    }

}