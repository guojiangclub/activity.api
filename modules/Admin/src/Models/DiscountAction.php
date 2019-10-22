<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/22
 * Time: 21:38
 */

namespace GuoJiangClub\Activity\Admin\Models;


use GuoJiangClub\Activity\Core\Models\Discount\Action;

class DiscountAction extends Action
{
    public function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = json_encode(['item' => $value]);
    }
}