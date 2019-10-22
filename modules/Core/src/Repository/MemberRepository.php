<?php

namespace GuojiangClub\Activity\Core\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuojiangClub\Activity\Core\Models\Member;

class MemberRepository extends BaseRepository
{
	public function model()
	{
		return Member::class;
	}
}