<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned(); //关联的订单号
            $table->integer('method_id')->unsigned()->nullable(); //使用的支付方式,暂时不使用
            $table->string('channel');  //支付渠道
            $table->integer('amount');   //本次支付的金额
            $table->string('status');
            $table->text('details')->nullable();  //存储json meta 数据
            $table->timestamp('paid_at')->nullable(); //支付时间
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
        Schema::drop('el_payment');
    }
}
