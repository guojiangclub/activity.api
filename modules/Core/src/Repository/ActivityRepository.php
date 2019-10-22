<?php

namespace GuoJiangClub\Activity\Core\Repository;

use GuoJiangClub\Activity\Core\Models\Activity;
use Prettus\Repository\Eloquent\BaseRepository;

class ActivityRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Activity::class;
    }

}
