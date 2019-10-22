<?php

namespace GuojiangClub\Activity\Server\Transformers;

class CouponTransformer extends BaseTransformer
{

    public static $excludeable = [
        'deleted_at'
    ];

    public function transformData($model)
    {
        $res = array_except($model->toArray(), self::$excludeable);
        return $res;
    }

}