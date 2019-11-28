<?php

namespace GuoJiangClub\Component\Favorite\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{

    protected $table = 'el_favorites';

    protected $guarded = ['id'];

    public function favoriteable()
    {
        return $this->morphTo();
    }

}