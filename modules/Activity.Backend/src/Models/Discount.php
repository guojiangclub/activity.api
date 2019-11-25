<?php

/*
 * This file is part of guojiangclub/activity-backend.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Backend\Models;

use Carbon\Carbon;

class Discount extends \GuoJiangClub\Activity\Core\Models\Discount\Discount
{
    public function rules()
    {
        return $this->hasMany(DiscountRule::class);
    }

    public function coupons()
    {
        return $this->hasMany(DiscountCoupon::class);
    }

    public function setCouponBasedAttribute($value)
    {
        if (2 == $this->type) {
            $this->attributes['coupon_based'] = 1;
        } else {
            $this->attributes['coupon_based'] = $value;
        }
    }

    public function setCodeAttribute($value)
    {
        if (1 == $this->coupon_based) {
            $this->attributes['code'] = $value;
        }
    }

    public function setPerUsageLimitAttribute($value)
    {
        if (1 == $this->coupon_based) {
            $this->attributes['per_usage_limit'] = $value;
        }
    }

    public function getTypeTextAttribute()
    {
        if (1 == $this->type) {
            return '订单折扣';
        }

        return '活动通行证';
    }

    public function getDiscountPaginate($where, $orWhere, $limit)
    {
        $data = $this->where(function ($query) use ($where) {
            if (count($where) > 0) {
                foreach ($where as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);
                    } else {
                        $query = $query->where($key, $value);
                    }
                }
            }
        })->where(function ($query) use ($orWhere) {
            if (count($orWhere) > 0) {
                foreach ($orWhere as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->orWhere($key, $operate, $va);
                    } else {
                        $query = $query->orWhere($key, $value);
                    }
                }
            }
        });

        if (0 == $limit) {
            return $data->get();
        }

        return $data->paginate($limit);
    }

    public function getDiscountCountByStatus($status)
    {
        $where = [];
        $orWhere = [];
        if (1 == $status) {
            $where['status'] = $status;
            $where['ends_at'] = ['>=', Carbon::now()];
        } else {
            $orWhere['status'] = $status;
            $orWhere['ends_at'] = ['<', Carbon::now()];
        }

        return $this->where(function ($query) use ($where) {
            if (count($where) > 0) {
                foreach ($where as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);
                    } else {
                        $query = $query->where($key, $value);
                    }
                }
            }
        })->where(function ($query) use ($orWhere) {
            if (count($orWhere) > 0) {
                foreach ($orWhere as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->orWhere($key, $operate, $va);
                    } else {
                        $query = $query->orWhere($key, $value);
                    }
                }
            }
        })->count();
    }

    public function getTakeRateAttribute()
    {
        if ($this->used <= 0 || $this->usage_limit <= 0) {
            return '0.00';
        }

        $number = ($this->used / $this->usage_limit) * 100;

        return number_format($number, 2, '.', '');
    }

    public function getUsedRateAttribute()
    {
        $useCount = $this->coupons()->whereNotNull('used_at')->count();
        if ($useCount <= 0 || $this->used <= 0) {
            return '0.00';
        }

        $number = ($useCount / $this->used) * 100;

        return number_format($number, 2, '.', '');
    }
}
