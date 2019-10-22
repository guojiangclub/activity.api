<?php

namespace GuojiangClub\Activity\Core\Models;

use ElementVip\Backend\Models\Admin;
use ElementVip\Component\User\Models\User;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Model;

class ActivityRefundLog extends Model implements Transformable
{
	use TransformableTrait;

	protected $table   = 'ac_activity_refund_log';
	protected $guarded = ['id'];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function getOperatorTextAttribute()
	{
		if ($this->admin_id == 9999) {
			return '管理员：系统自动处理';
		}

		if ($this->admin_id > 0) {
			return '管理员:' . $this->admin->name;
		}

		if ($this->user) {
			return '用户:' . ($this->user->name ? $this->user->name : $this->user->mobile);
		}

		return '';
	}

	/**
	 * 后台退换货动作说明
	 *
	 * @return string
	 */
	public function getActionTextAttribute()
	{
		switch ($this->attributes['action']) {
			case  'create':
				return '创建申请';
				break;

			case 'agree':
				return '同意申请';
				break;

			case 'agree_nosend':
				return '同意申请';
				break;

			case  'refuse':
				return '拒绝申请';
				break;

			case  'cmp_refuse':
				return '拒绝申请';
				break;

			case  'cancel':
				return '取消申请';
				break;

			case 'receipt':
				return '已完成';
				break;

			case 'autoCancel':
				return '系统自动关闭';
				break;

			case 'close':
				return '申请关闭';
				break;
		}

		return '管理员修改';
	}
}