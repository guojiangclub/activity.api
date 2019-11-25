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

use GuoJiangClub\Activity\Core\Models\Refund;
use Prettus\Repository\Eloquent\BaseRepository;

class ActivityRefundRepository extends BaseRepository
{
    public function model()
    {
        return Refund::class;
    }

    public function getRefundsPaginated($view, $where, $value, $time = [], $complete_time = [], $limit = 15)
    {
        return $this->scopeQuery(function ($query) use ($where, $view, $time, $complete_time) {
            if (is_array($where) and count($where) > 0) {
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

            if ('all' !== $view) {
                switch ($view) {
                    case 2: //已完成
                        $query = $query->whereIn('status', [2, 3]);
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
            if ($value) {
                $query->where('order_no', 'like', '%'.$value.'%');
            }
        })->paginate($limit);
    }
}
