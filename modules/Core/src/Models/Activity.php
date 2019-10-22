<?php

namespace GuojiangClub\Activity\Core\Models;

use Carbon\Carbon;
use ElementVip\Component\Discount\Contracts\DiscountSubjectContract;
use ElementVip\Component\Point\Model\Point;
use ElementVip\Component\Product\Models\Goods;
use ElementVip\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Activity extends Model
{
	use SoftDeletes;

	const FEE_TYPE_CHARGING        = 'CHARGING';
	const FEE_TYPE_PASS            = 'PASS';
	const FEE_TYPE_OFFLINE_CHARGES = 'OFFLINE_CHARGES';

	protected $table   = 'ac_activity';
	protected $guarded = ['id'];
	protected $appends = ['coach', 'signed_count', 'can_reward', 'can_reward_limit', 'can_sign'];

	public function statement()
	{
		return $this->hasOne(Statement::class, 'id', 'statement_id');
	}

	public function city()
	{
		return $this->belongsTo(City::class);
	}

	public function members()
	{
		return $this->hasMany(Member::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function form()
	{
		return $this->hasOne(ActivityForm::class, 'id', 'form_id');
	}

	public function isPassType($type = 3)
	{
		return $this->payments()->where('type', $type)->first();
	}

	public function coach()
	{
		if ($coachMember = $this->members()->where('role', 'coach')->first()) {
			$coachUserId = $coachMember->user_id;
			if ($coach = User::find($coachUserId)) {
				$coach->nick_name = $coach->getUserAttr('coach_name') ?: $coach->nick_name;
				$coach->title     = $coach->getUserAttr('title');
				$coach->describe  = $coach->getUserAttr('describe');

				return $coach;
			}
		}

		return false;
	}

	public function liked()
	{
		return $this->hasMany(Like::class);
	}

	public function points()
	{
		return $this->hasMany(ActPoint::class);
	}

	public function getCoachAttribute()
	{
		return $this->coach();
	}

	public function recalculateMemberCount()
	{
		$this->member_count = $this->members()->where('role', 'user')->whereIn('status', [1, 2, 4])->count();
		$this->save();
	}

	public function recalculateLikeCount()
	{
		$this->like_count = $this->liked()->where('status', 1)->count();
		$this->save();
	}

	public function canCancel()
	{
		if ($this->refund_term == null AND $this->starts_at > Carbon::now()) {
			return true;
		}

		if ($this->refund_term != null AND $this->starts_at > date('Y-m-d H:i:s', strtotime('+' . $this->refund_term . ' minute'))) {
			return true;
		}

		return false;
	}

	public function getSignedCountAttribute()
	{
		return $this->members()->where('status', 2)->count();
	}

	public function getCanRewardAttribute()
	{
		if ($point = $this->points()->where('type', 'coach_rewards')->first()) {
			if ($this->ends_at < date('Y-m-d H:i:s', strtotime('-' . $point->limit . ' day'))) {
				return -1;
			}
			$LimitValue    = $point->value;
			$memberIds     = $this->members()->pluck('user_id')->toArray();
			$rewardedValue = Point::whereIn('user_id', $memberIds)->where('item_type', Activity::class)->where('item_id', $this->id)->sum('value');
			$canReward     = $LimitValue - $rewardedValue;

			return $canReward < 0 ? 0 : $canReward;
		}

		return -2;
	}

	public function getCanRewardLimitAttribute()
	{
		if ($point = $this->points()->where('type', 'coach_rewards')->first()) {
			return date('Y-m-d H:i:s', strtotime($this->ends_at . '+' . $point->limit . ' day'));
		}

		return null;
	}

	public function getCanSignAttribute()
	{
		$end_at = new Carbon($this->ends_at);
		if ($this->status == 2 OR ($this->status == 3 AND Carbon::now() < $end_at->addMinutes($this->delay_sign))) {
			return true;
		} else {
			return false;
		}
	}

	public function goods()
	{
		return $this->belongsToMany(Goods::class, 'ac_activity_goods', 'activity_id', 'goods_id')->withPivot(['required', 'rate', 'price']);
	}

}
