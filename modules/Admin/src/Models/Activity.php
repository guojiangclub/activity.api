<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/22
 * Time: 20:20
 */

namespace GuojiangClub\Activity\Admin\Models;


class Activity extends \ElementVip\Activity\Core\Models\Activity
{
    protected $appends = ['type_text'];

    public function getTypeTextAttribute()
    {
        switch ($this->type)
        {
            case 'TRAIN':
                return '训练';
            break;

            case 'MATCH':
                return '赛事';
            break;
            
            default:
                return '旅行';
        }
    }
}