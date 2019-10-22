<?php

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