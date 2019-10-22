<?php

namespace GuojiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityCategory extends Model
{
	use SoftDeletes;

	protected $table   = 'ac_activity_category';
	protected $guarded = ['id'];
}