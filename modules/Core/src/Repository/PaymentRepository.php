<?php

namespace GuoJiangClub\Activity\Core\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Activity\Core\Models\Payment;

class PaymentRepository extends BaseRepository
{
	public function model()
	{
		return Payment::class;
	}
}