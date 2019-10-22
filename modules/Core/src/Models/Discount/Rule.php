<?php
namespace GuojiangClub\Activity\Core\Models\Discount;

use Illuminate\Database\Eloquent\Model;
use ElementVip\Component\Discount\Models\Rule as BaseRule;

class Rule extends BaseRule
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $table = 'ac_discount_rule';
}