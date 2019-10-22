<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/22
 * Time: 21:38
 */

namespace GuojiangClub\Activity\Admin\Models;


use ElementVip\Activity\Core\Models\Discount\Action;

class DiscountAction extends Action
{
    public function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = json_encode(['item' => $value]);
    }
}