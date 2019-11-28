<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Server\Services;

use GuoJiangClub\Activity\Server\Overtrue\AccessToken as CoreAccessToken;

class MiniAccessToken extends CoreAccessToken
{
    protected $prefix = 'activity.mini.program.access_token.';
}
