<?php

namespace GuojiangClub\Activity\Server\Transformers;

use Carbon\Carbon;

class ActivityTransformer extends BaseTransformer
{
	public static $excludeable = [
		'deleted_at',
	];

	protected $day = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

	public function transformData($model)
	{
		if ($model->status == 1 AND $model->member_limit != null AND $model->member_limit <= $model->member_count) {
			$model->status = 5;
		}

		if ($model->entry_end_at > Carbon::now() AND $model->starts_at > Carbon::now()) {
			$model->status = 1;
		} else {
			if ($model->entry_end_at < Carbon::now() AND $model->starts_at > Carbon::now()) {
				$model->status = 4;
			} else {
				if ($model->starts_at < Carbon::now() AND $model->ends_at > Carbon::now()) {
					$model->status = 2;
				} else {
					if ($model->ends_at < Carbon::now()) {

						$model->status = 3;
					}
				}
			}
		}

		switch ($model->status) {
			case 0 :
				$model->status_text = '活动未启用';
				break;
			case 1 :
				$model->status_text = '活动报名中';
				break;
			case 2 :
				$model->status_text = '活动进行中';
				break;
			case 3 :
				$model->status_text = '活动已结束';
				break;
			case 4 :
				$model->status_text = '报名已截止';
				break;
			case 5 :
				$model->status_text = '活动已满额';
				break;
			default :
				break;
		}

		if ($canceled = request('canceled') AND $canceled == 1) {
			if ($user = request()->user() AND $member = $model->members()->where('user_id', $user->id)->where('role', 'user')->where('status', 3)->orderBy('created_at', 'desc')->first()) {
				$model->member_status     = $member->status;
				$model->member_pay_status = $member->pay_status;
			}
		} else {
			if ($user = auth('api')->user() AND $member = $model->members()->where('user_id', $user->id)->where('role', 'user')->orderBy('created_at', 'desc')->first()) {
				$model->member_status     = $member->status;
				$model->member_pay_status = $member->pay_status;
			}
		}

		$model->payments     = $model->payments()->first();
		$model->time_section = $this->getFormatTime($model->starts_at, $model->ends_at);
		$model->has_form     = false;
		if (($model->statement_id != 0) || (isset($model->form) && $model->form)) {
			$model->has_form = true;
		}

		$res = array_except($model->toArray(), self::$excludeable);

		return $res;
	}

	public function getFormatTime($starts_at, $ends_at)
	{
		$starts_at_time = strtotime($starts_at);
		$starts_at_day  = $this->day[date('w', $starts_at_time)];
		$ends_at_time   = strtotime($ends_at);
		$ends_at_day    = $this->day[date('w', $ends_at_time)];
		if (date('Y-m-d', $starts_at_time) == date('Y-m-d', $ends_at_time)) {
			return date('Y/m/d', $starts_at_time) . ' ' . $starts_at_day . ' ' . date('H:i', $starts_at_time) . '-' . date('H:i', $ends_at_time);
		} else {
			return date('Y/m/d', $starts_at_time) . ' ' . $starts_at_day . ' ' . date('H:i', $starts_at_time) . ' - ' .
				date('Y/m/d', $ends_at_time) . ' ' . $ends_at_day . ' ' . date('H:i', $ends_at_time);
		}
	}
}