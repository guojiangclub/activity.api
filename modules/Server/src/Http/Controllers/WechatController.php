<?php

namespace GuojiangClub\Activity\Server\Http\Controllers;

use Log;

class WechatController extends Controller
{
	/**
	 * 处理微信的请求消息
	 *
	 * @return string
	 */
	public function serve()
	{
		//Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
		// return '';
		$wechat = app('wechat');
		$wechat->server->setMessageHandler(function($message){
			return "欢迎关注 overtrue！";
		});

		Log::info('return response.');

		return $wechat->server->serve();
	}

	public function getJsConfig()
	{
	   return  app('wechat.channel')->getJsConfig(request('url'),settings('activity_app_id'));
		/*$wechat->js->setUrl(request('url'));
		if($method = request('method') AND is_array($method)){
            return $wechat->js->config($method);
        }
		return $wechat->js->config(array());*/
	}
}