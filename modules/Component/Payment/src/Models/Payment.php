<?php
namespace GuoJiangClub\Component\Payment\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/7
 * Time: 16:35
 */
class Payment extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_VOID = 'void';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_UNKNOWN = 'unknown';

    protected $table = 'el_payment';

    protected $appends = ['channel_text', 'amount_yuan'];

    protected $guarded = ['id'];

    public function getChannelTextAttribute()
    {
        $str='';
        if($this->channel_no=='test'){
            $str='测试';
        }
        switch ($this->channel) {
            case 'test':
                return "测试";
                break;
            case 'alipay_wap':
                return "支付宝".$str;
                break;
            case 'alipay_pc_direct':
                return "支付宝".$str;
                break;
            case 'wx_pub':
                return "微信".$str;
                break;
            case 'wx_pub_qr':
                return "微信".$str;
                break;

            case 'wx_lite':
                return "微信".$str;
                break;
            case 'balance':
                return "余额";
                break;
            case 'pop_cash_pay':
                return "刷卡";
                break;
            case  'cash_pay':
                return '现金';
                break;
            case 'ali_scan_pay':
                return '支付宝';
                break;
            default:
                return '';

        }
    }

    public function getAmountYuanAttribute()
    {
        return number_format($this->amount / 100, 2, ".", "");
    }
}