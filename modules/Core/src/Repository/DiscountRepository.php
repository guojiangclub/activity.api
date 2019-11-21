<?php

namespace GuoJiangClub\Activity\Core\Repository;

use Prettus\Repository\Contracts\RepositoryInterface;

interface DiscountRepository extends \iBrand\Component\Discount\Repositories\DiscountRepository
{

    public function getDiscountByCode($code, $isCoupon = false);
}
