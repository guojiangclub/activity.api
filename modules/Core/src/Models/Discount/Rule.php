<?php
namespace GuoJiangClub\Activity\Core\Models\Discount;

use Illuminate\Database\Eloquent\Model;
use iBrand\Component\Discount\Models\Rule as BaseRule;

class Rule extends BaseRule
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $table = 'ac_discount_rule';
}