<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-10-22
 * Time: 19:07
 */

namespace GuoJiangClub\Activity\Core\Models;

use GuoJiangClub\Activity\Core\Models\Traits\EntrustUserTrait;
use iBrand\Component\Point\Models\Point;
use \iBrand\Component\User\Models\User as BaseUser;

class User extends BaseUser
{
    use EntrustUserTrait;

    public function attr()
    {
        return $this->hasMany(UserAttr::class);
    }

    public function getUserAttr($key)
    {
        if (!empty($key)) {
            if ($attr = $this->attr()->where('key', $key)->orderBy('created_at', 'desc')->first()) {
                return $attr->value;
            }
        }
        return false;
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }
}