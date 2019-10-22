<?php

namespace GuojiangClub\Activity\Server\Services;

use Storage;

class MiniProgramService
{
	protected $token;

	const API_WXACODE_GET = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='; //小程序接口B

	public function __construct()
	{
		$app_id      = app('system_setting')->getSetting('activity_mini_program_app_id');
		$app_secret  = app('system_setting')->getSetting('activity_mini_program_secret');
		$this->token = new MiniAccessToken($app_id, $app_secret);
	}

	protected function getAccessToken()
	{
		return $this->token->getToken();
	}

	public function createMiniQrcode($page, $width, $scene = '')
	{
		$img_name = $scene . '_' . 'activity_mini_qrcode.jpg';
		$savePath = 'activity/mini/qrcode/' . $img_name;
		if (Storage::disk('public')->exists($savePath)) {
			return $savePath;
		}

		$option = [
			'page'  => $page,
			'width' => $width,
			'scene' => $scene,
		];

		$body = $this->mini_curl(self::API_WXACODE_GET . $this->getAccessToken(), $option);
		if (str_contains($body, 'errcode')) {
			return false;
		}

		$result = Storage::disk('public')->put($savePath, $body);
		if ($result) {
			return $savePath;
		}

		return false;
	}

	function mini_curl($url, $optData = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		if (!empty($optData)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($optData));
		}

		$res = curl_exec($ch);
		curl_close($ch);

		return $res;
	}

}