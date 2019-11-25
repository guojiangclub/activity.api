<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'routeAttributes' => [
        'middleware' => ['api', 'cors'],
    ],

    'routeAuthAttributes' => [
        'middleware' => ['auth:api'],
    ],
];
