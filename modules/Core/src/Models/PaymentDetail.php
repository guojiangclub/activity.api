<?php

namespace GuojiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/7
 * Time: 16:35
 */
class PaymentDetail extends Model
{
	const STATUS_NEW       = 'new';
	const STATUS_COMPLETED = 'completed';
	const STATUS_CANCELLED = 'cancelled';
	const STATUS_VOID      = 'void';
	const STATUS_REFUNDED  = 'refunded';
	const STATUS_UNKNOWN   = 'unknown';

	protected $table = 'ac_activity_payment_detail';

	protected $appends = ['channel_text'];

	protected $guarded = ['id'];

	public function getChannelTextAttribute()
	{
		switch ($this->channel) {
			case 'test':
				return "测试";
				break;
			case 'alipay_wap':
				return "支付宝";
				break;
			case 'alipay_pc_direct':
				return "支付宝";
				break;
			case 'wx_pub':
				return "微信";
				break;
			case 'wx_pub_qr':
				return "微信";
				break;

			case 'balance':
				return "余额";
				break;
			default:
				return '';
		}
	}
}