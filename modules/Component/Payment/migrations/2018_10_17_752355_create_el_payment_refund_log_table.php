<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElPaymentRefundLogTable extends Migration
{
    /**
     * Run the migrations.
     * 支付记录
     * @return void
     */
    public function up()
    {
        Schema::create('el_payment_refund_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action');  //create_refund 创建退款请求；query_refund 查询退款
            $table->dateTime('operate_time');   //提交时间
            $table->string('refund_no')->nullable();   //退款编号
            $table->string('order_no')->nullable(); //订单编号
            $table->string('refund_id')->nullable();  //交易流水号
            $table->integer('amount')->default(0); //退款金额
            $table->string('type')->nullable(); //订单类型：order，activity，recharge
            $table->string('channel')->nullable(); //支付渠道 wx_pub_qr,wx_pub,wx_lite,alipay
            $table->string('status')->nullable(); //状态：state，success，failed
            $table->mediumText('meta')->nullable();  //记录微信、支付宝退款提交之后返回的所有数据
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
        Schema::drop('el_payment_refund_log');
    }
}
