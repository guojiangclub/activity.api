<?php

namespace GuoJiangClub\Activity\Core\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Activity\Core\Models\Member;

class MemberRepository extends BaseRepository
{
	public function model()
	{
		return Member::class;
	}
}