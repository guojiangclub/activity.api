<?php
namespace GuoJiangClub\Activity\Core\Repository\Eloquent;

use Carbon\Carbon;
use GuoJiangClub\Activity\Core\Models\Discount\Discount;
use GuoJiangClub\Activity\Core\Repository\DiscountRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class DiscountRepositoryEloquent extends \ElementVip\Component\Discount\Repositories\Eloquent\DiscountRepositoryEloquent implements DiscountRepository
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

}