<?php
namespace GuoJiangClub\Activity\Core\Repository\Eloquent;

use Carbon\Carbon;
use GuoJiangClub\Activity\Core\Models\Discount\Discount;
use GuoJiangClub\Activity\Core\Repository\DiscountRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class DiscountRepositoryEloquent extends \iBrand\Component\Discount\Repositories\Eloquent\DiscountRepositoryEloquent implements DiscountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Discount::class;
    }

    /**
     * get discount by code
     * @param bool $isCoupon
     * @return mixed
     */
    public function getDiscountByCode($code, $isCoupon = false)
    {
        if (empty($code))
            return false;

        return $this->model->where('status', 1)->where('coupon_based', $isCoupon)
            ->where('code', $code)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere(function ($query) {
                        $query->where('starts_at', '<', Carbon::now());
                    });
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere(function ($query) {
                        $query->where('ends_at', '>', Carbon::now());
                    });
            })->with('rules', 'actions')->get()->first();
    }

}