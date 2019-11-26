<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElMerchantPayTable extends Migration
{
    /**
     * Run the migrations.
     * 企业打款记录
     * @return void
     */
    public function up()
    {
        Schema::create('el_merchant_pay', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin_type');  //打款类型：REFUND 退款;COMMISSION 分销佣金
            $table->integer('origin_id');
            $table->string('channel')->default('wechat'); //打款渠道：wechat 微信； alipay 支付宝
            $table->integer('channel_id')->default(0); //如果是REFUND，记录el_refund_amount 的ID
            $table->string('partner_trade_no'); //打款编号
            $table->string('payment_no')->nullable(); //交易流水号
            $table->integer('amount');  //金额
            $table->string('status');  //打款状态:SUCCESS FAIL
            $table->string('error_code')->nullable();   //失败状态码：NAME_MISMATCH
            $table->string('err_code_des')->nullable(); //失败描述：真实姓名不一致
            $table->dateTime('payment_time')->nullable();   //成功打款时间
            $table->integer('user_id'); //用户ID
            $table->integer('admin_id');    //操作人ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('el_merchant_pay');
    }
}
