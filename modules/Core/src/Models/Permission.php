<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-11-30
 * Time: 12:00
 */

namespace GuoJiangClub\Activity\Core\Models;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $fillable = ['name', 'display_name', 'description'];

}