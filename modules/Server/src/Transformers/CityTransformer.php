<?php

namespace GuoJiangClub\Activity\Server\Transformers;


class CityTransformer extends BaseTransformer
{

    public static $excludeable = [
        'deleted_at'
    ];

    public function transformData($model)
    {
        $acts = $model->activity()->count();
        $model->activities = $acts;
        $res = array_except($model->toArray(), self::$excludeable);

        return $res;
    }

}