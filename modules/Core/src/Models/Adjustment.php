<?php

namespace GuojiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ElementVip\Component\User\Models\User;

class Adjustment extends Model
{
    use SoftDeletes;

    const ACTIVITY_DISCOUNT_ADJUSTMENT = 'activity_discount';

    protected $table = 'ac_activity_adjustment';
    protected $guarded = ['id'];

}
