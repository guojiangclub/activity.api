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

use GuoJiangClub\Activity\Core\Models\ActivityCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ActivityCategory::all();

        return $this->api($categories, true, 200);
    }
}
