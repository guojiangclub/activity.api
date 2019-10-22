<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/22
 * Time: 21:37
 */

namespace GuojiangClub\Activity\Admin\Models;


use ElementVip\Activity\Core\Models\Discount\Rule;

class DiscountRule extends Rule
{
    public function setConfigurationAttribute($value)
    {
        $type = $this->attributes['type'];

        if ($type == 'contains_activity') {

            if($value == 'all'){
                $data['items'] = 'all';
            } else {
                $data['items'] = explode(',',$value);
            }

            $this->attributes['configuration'] = json_encode($data);

        }

    }

    public function getConfigurationAttribute()
    {
        $type = $this->attributes['type'];
        $value = json_decode($this->attributes['configuration'], true);
       

        if ($type == 'contains_activity') {
            if($value['items'] == 'all'){
                return 'all';
            }else{
                return implode(',', $value['items']);
            }
        } 
    }
}