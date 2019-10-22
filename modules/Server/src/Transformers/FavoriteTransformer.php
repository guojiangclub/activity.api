<?php

namespace GuoJiangClub\Activity\Server\Transformers;

class FavoriteTransformer extends BaseTransformer
{

    public static $excludeable = [
        'deleted_at'
    ];

    public function transformData($model)
    {
        switch ($model->favoriteable->status) {
            case 0 : $model->favoriteable->status_text = '未启用';
                break;
            case 1 : $model->favoriteable->status_text = '报名中';
                break;
            case 2 : $model->favoriteable->status_text = '进行中';
                break;
            case 3 : $model->favoriteable->status_text = '已结束';
                break;
            case 4 : $model->favoriteable->status_text = '报名截止';
                break;
            default : break;
        }
        if ($user = request()->user() AND $member = $model->favoriteable->members()->where('user_id', $user->id)->first()) {
            $model->favoriteable->member_status = $member->status;
        }
        $res = array_except($model->toArray(), self::$excludeable);

        return $res;
    }

}