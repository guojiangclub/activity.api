<?php

namespace GuojiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $table = 'ac_activity_city';
    protected $guarded = ['id'];

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function getActCountAttribute()
    {
        return $this->activity()->count();
    }
}
