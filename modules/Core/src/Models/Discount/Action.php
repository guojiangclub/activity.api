<?php
namespace GuoJiangClub\Activity\Core\Models\Discount;

use Illuminate\Database\Eloquent\Model;
use iBrand\Component\Discount\Models\Action as BaseAction;

class Action extends BaseAction
{
    protected $table = 'ac_discount_action';

}