<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElPaymentLogTable extends Migration
{
    /**
     * Run the migrations.
     * 支付记录
     * @return void
     */
    public function up()
    {
        Schema::create('el_payment_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action');  //create_charge 创建支付请求；result_pay 支付之后
            $table->dateTime('operate_time');   //提交时间/支付时间
            $table->string('order_no')->nullable();   //订单号
            $table->string('transcation_order_no')->nullable();  //提交给微信的新的订单号
            $table->string('transcation_no')->nullable();  //交易流水号
            $table->integer('amount')->default(0); //订单金额
            $table->string('channel')->nullable(); //支付渠道 wx_pub_qr,wx_pub,wx_lite,alipay
            $table->string('type')->nullable(); //订单类型：order，activity，recharge
            $table->string('status')->nullable(); //状态：state，success，failed
            $table->integer('user_id')->default(0); //用户ID
            $table->mediumText('meta')->nullable();  //记录微信、支付宝之后成功之后返回的所有数据
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
        Schema::drop('el_payment_log');
    }
}
