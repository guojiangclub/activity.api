<?php

namespace GuojiangClub\Activity\Server\Services;

use ElementVip\Wechat\Server\Overtrue\AccessToken as CoreAccessToken;

class MiniAccessToken extends CoreAccessToken
{
	protected $prefix = 'activity.mini.program.access_token.';

}