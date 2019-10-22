<?php

namespace GuojiangClub\Activity\Core\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuojiangClub\Activity\Core\Models\Payment;

class PaymentRepository extends BaseRepository
{
	public function model()
	{
		return Payment::class;
	}
}