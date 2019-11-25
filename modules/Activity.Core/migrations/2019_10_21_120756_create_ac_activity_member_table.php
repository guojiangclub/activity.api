<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcActivityMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_activity_member', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no', 64)->comment('订单号');
            $table->integer('activity_id');
            $table->integer('user_id');
            $table->string('role')->nullable()->default('user');
            $table->string('address')->nullable();
            $table->dateTime('joined_at')->nullable()->comment('报名时间');
            $table->dateTime('signed_at')->nullable()->comment('签到时间');
            $table->dateTime('cancel_at')->nullable()->comment('报名取消时间');
            $table->integer('status')->nullable()->default(1);
            $table->integer('pay_status')->default(0)->comment('支付状态');
            $table->integer('payment_id')->nullable();
            $table->integer('price')->default(0);
            $table->integer('total')->default(0)->comment('活动总价');
            $table->integer('point')->default(0);
            $table->dateTime('remind_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ac_activity_member');
    }
}
