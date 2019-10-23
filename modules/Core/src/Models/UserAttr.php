<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-10-22
 * Time: 19:21
 */

namespace GuoJiangClub\Activity\Core\Models;


use Illuminate\Database\Eloquent\Model;

class UserAttr extends Model
{
    protected $table = 'ac_user_attr';

    protected $fillable = ['key', 'value'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}