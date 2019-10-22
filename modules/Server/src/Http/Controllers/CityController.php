<?php

namespace GuojiangClub\Activity\Server\Http\Controllers;

use ElementVip\Activity\Core\Models\City;
use GuojiangClub\Activity\Server\Transformers\CityTransformer;

class CityController extends Controller
{

    public function index()
    {
        $limit = request('limit') ? request('limit') : 15;
        return $this->response->paginator(City::paginate($limit),new CityTransformer());
    }

    public function countCity()
    {
        return $this->api(City::all()->count());
    }

}