<?php

namespace GuojiangClub\Activity\Core\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuojiangClub\Activity\Core\Models\ActivityForm;

class ActivityFormRepository extends BaseRepository
{
	public function model()
	{
		return ActivityForm::class;
	}
}