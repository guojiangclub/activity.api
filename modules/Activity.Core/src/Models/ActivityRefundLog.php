<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Models;

use GuoJiangClub\Backend\Models\Admin;
use GuoJiangClub\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class ActivityRefundLog extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'ac_activity_refund_log';
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
        if (9999 == $this->admin_id) {
            return '管理员：系统自动处理';
        }

        if ($this->admin_id > 0) {
            return '管理员:'.$this->admin->name;
        }

        if ($this->user) {
            return '用户:'.($this->user->name ? $this->user->name : $this->user->mobile);
        }

        return '';
    }

    /**
     * 后台退换货动作说明.
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
