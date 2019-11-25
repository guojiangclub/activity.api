<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Repository;

use GuoJiangClub\Activity\Core\Models\ActivityForm;
use Prettus\Repository\Eloquent\BaseRepository;

class ActivityFormRepository extends BaseRepository
{
    public function model()
    {
        return ActivityForm::class;
    }
}
