<?php

namespace GuoJiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;

class UserSize extends Model
{

    public $timestamps = false;

    protected $table = 'ac_user_size';

    protected $fillable = ['upper', 'lower', 'shoes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}