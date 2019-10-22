<?php

namespace GuojiangClub\Activity\Core\Models;

use ElementVip\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
	use SoftDeletes;

	const STATUS_AUDIT     = 0;//待审核
	const STATUS_PASS      = 1;//审核通过
	const STATUS_REFUSE    = 2;//拒绝申请
	const STATUS_COMPLETE  = 3;//已完成
	const STATUS_CANCEL    = 4;//已关闭
	const STATUS_SHOP_PAID = 8;    //等待商家退款
	const TYPE_REFUND      = 1;    //  仅退款

	protected $table = 'ac_activity_refund';

	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		$this->status    = self::STATUS_AUDIT;
		$this->refund_no = build_order_no('ACR');
	}

	public function order()
	{
		return $this->belongsTo(Member::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function refundLog()
	{
		return $this->hasMany(ActivityRefundLog::class);
	}

	public function getStatusTextAttribute()
	{
		switch ($this->attributes['status']) {
			case 0:
				return '待处理';
				break;

			case 1:
				return '审核通过';
				break;

			case 2:
				return '拒绝申请';
				break;

			case 3:
				return '已完成';
				break;

			default:
				return '待退款';
		}
	}

	/**
	 * 后台退换货申请详情页按钮
	 *
	 * @return string
	 */
	public function getActionBtnTextAttribute()
	{
		$status = $this->attributes['status'];
		switch ($status) {
			case 0:
				return '<button type="submit" class="btn btn-primary">提交审核</button>';
				break;

			case 8:
				return '<button type="submit" class="btn btn-primary">确认已退款</button>';
				break;
			default:
				return '';
		}
	}
}