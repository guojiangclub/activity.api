<?php

namespace GuojiangClub\Activity\Core\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuojiangClub\Activity\Core\Models\Refund;

class ActivityRefundRepository extends BaseRepository
{
	public function model()
	{
		return Refund::class;
	}

	public function getRefundsPaginated($view, $where, $value, $time = [], $complete_time = [], $limit = 15)
	{
		return $this->scopeQuery(function ($query) use ($where, $view, $time, $complete_time) {
			if (is_array($where) AND count($where) > 0) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($operate, $va) = $value;
						$query = $query->where($key, $operate, $va);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			if (count($time) > 0) {
				$query = $query->whereBetween('created_at', $time);
			}

			if (count($complete_time) > 0) {
				$query = $query->whereBetween('updated_at', $complete_time);
			}

			if ($view !== 'all') {
				switch ($view) {
					case 2: //已完成
						$query = $query->whereIn('status', [2,3]);
						break;

					case 8: //待退款
						$query = $query->where('status', 8);
						break;

					default:    //待审核
						$query = $query->where('status', 0);
				}
			}

			return $query->orderBy('updated_at', 'desc');
		})->whereHas('order', function ($query) use ($value) {
			if($value){
				$query->where('order_no', 'like', '%' . $value . '%');
			}
		})->paginate($limit);
	}
}