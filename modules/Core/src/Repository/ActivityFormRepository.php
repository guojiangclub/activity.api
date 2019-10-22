<?php

namespace GuoJiangClub\Activity\Core\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Activity\Core\Models\ActivityForm;

class ActivityFormRepository extends BaseRepository
{
	public function model()
	{
		return ActivityForm::class;
	}
}