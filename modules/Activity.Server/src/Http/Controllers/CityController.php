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

use GuoJiangClub\Activity\Core\Models\City;
use GuoJiangClub\Activity\Server\Transformers\CityTransformer;

class CityController extends Controller
{
    public function index()
    {
        $limit = request('limit') ? request('limit') : 15;

        return $this->response->paginator(City::paginate($limit), new CityTransformer());
    }

    public function countCity()
    {
        return $this->api(City::all()->count());
    }
}
