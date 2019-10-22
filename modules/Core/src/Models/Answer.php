<?php

namespace GuoJiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
	use SoftDeletes;

	protected $table   = 'ac_activity_form_answer';
	protected $guarded = ['id'];
}