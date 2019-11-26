<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Models;

use iBrand\Component\Discount\Contracts\DiscountSubjectContract;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Point\Model\Point;
use iBrand\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Member extends Model implements DiscountSubjectContract
{
    use SoftDeletes;

    const STATUS_WAIT = 0; //待支付
    const STATUS_PAY = 1; //已支付
    const STATUS_CANCEL = 2; //已取消订单
    const STATUS_INVALID = 3; //已作废订单
    const STATUS_REFUND = 4; //待审核（用于现金支付）
    const STATUS_DELETED = 5; //已删除订单

    protected $table = 'ac_activity_member';
    protected $guarded = ['id'];
    protected $appends = ['rewarded', 'avatar'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentDetail()
    {
        return $this->hasOne(PaymentDetail::class, 'order_id', 'id');
    }

    public function adjustment()
    {
        return $this->hasOne(Adjustment::class);
    }

    public function getNameAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (Str::contains($value, 'base64:')) {
            return base64_decode(str_replace('base64:', '', $value));
        }

        return $value;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = 'base64:'.base64_encode($value);
    }

    public function getRewardedAttribute()
    {
        if (Point::where('user_id', $this->user_id)->where('item_type', Activity::class)->where('item_id', $this->activity_id)->first()) {
            return 1;
        }

        return 0;
    }

    public function getAvatarAttribute()
    {
        return $this->user ? $this->user->avatar : null;
    }

    public function getLikedAttribute()
    {
        if (Like::where('user_id', $this->user_id)->where('favoriteable_id', $this->activity_id)->where('favoriteable_type', 'activity')->first()) {
            return 1;
        }

        return 0;
    }

    /**
     * get subject total amount.
     *
     * @return int
     */
    public function getSubjectTotal()
    {
        // TODO: Implement getSubjectTotal() method.
    }

    /**
     * get subject count item.
     *
     * @return int
     */
    public function getSubjectCount()
    {
        // TODO: Implement getSubjectCount() method.
    }

    /**
     * get subject items.
     *
     * @return mixed
     */
    public function getItems()
    {
        // TODO: Implement getItems() method.
    }

    /**
     * get subject count.
     *
     * @return mixed
     */
    public function countItems()
    {
        // TODO: Implement countItems() method.
    }

    /**
     * @param $adjustment
     *
     * @return mixed
     */
    public function addAdjustment($adjustment)
    {
    }

    /**
     * get subject user.
     *
     * @return mixed
     */
    public function getSubjectUser()
    {
        // TODO: Implement getSubjectUser() method.
    }

    /**
     * get current total.
     *
     * @return mixed
     */
    public function getCurrentTotal()
    {
        // TODO: Implement getCurrentTotal() method.
    }

    /**
     * get subject is paid.
     *
     * @return mixed
     */
    public function isPaid()
    {
        // TODO: Implement isPaid() method.
    }

    public function shopOrder()
    {
        return $this->belongsToMany(Order::class, 'ac_activity_orders', 'member_id', 'order_id');
    }
}
