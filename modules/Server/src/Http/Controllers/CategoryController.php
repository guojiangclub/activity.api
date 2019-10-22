<?php

namespace GuojiangClub\Activity\Server\Http\Controllers;

use ElementVip\Activity\Core\Models\ActivityCategory;

class CategoryController extends Controller
{

	public function index()
	{
		$categories = ActivityCategory::all();

		return $this->api($categories, true, 200);
	}
}