<?php
namespace GuoJiangClub\Activity\Core\Repository;


use iBrand\Component\Discount\Contracts\DiscountContract;
use Prettus\Repository\Contracts\RepositoryInterface;

interface CouponRepository extends \iBrand\Component\Discount\Repositories\CouponRepository
{

    public function canGetCoupon(DiscountContract $discount, $user);
    
    public function canGetCouponByMonth(DiscountContract $discount, $user);
    
    public function canGetCouponNumByMonth(DiscountContract $discount, $user);

}
