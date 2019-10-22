<?php

namespace GuoJiangClub\Activity\Core\Models;

use ElementVip\Component\Favorite\Models\Favorite;
use ElementVip\Component\User\Models\User;

class Like extends Favorite
{

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
