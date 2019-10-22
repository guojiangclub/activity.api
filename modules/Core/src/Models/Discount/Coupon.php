<?php
namespace GuojiangClub\Activity\Core\Models\Discount;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;
use ElementVip\Component\Discount\Models\Coupon as BaseCoupon;

class Coupon extends BaseCoupon
{
    protected $table = 'ac_discount_coupon';
    protected $appends = ['discount_amount','discount_percentage','starts_at','ends_at'];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

}