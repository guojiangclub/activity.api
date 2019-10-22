<?php
namespace GuojiangClub\Activity\Core\Models\Discount;

use Carbon\Carbon;
use ElementVip\Component\Discount\Models\Discount as BaseDiscount;


class Discount extends BaseDiscount
{
    protected $table = 'ac_discount';

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}