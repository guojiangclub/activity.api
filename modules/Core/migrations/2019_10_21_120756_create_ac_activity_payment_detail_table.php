<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcActivityPaymentDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_activity_payment_detail', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order_id')->unsigned()->nullable()->comment('关联的订单号');
			$table->integer('method_id')->unsigned()->nullable()->comment('使用的支付方式,暂时不使用');
			$table->string('pingxx_no')->nullable()->comment('ping++支付订单号');
			$table->string('channel', 64)->nullable()->comment('支付渠道');
			$table->string('channel_no')->nullable();
			$table->integer('amount')->unsigned()->nullable()->comment('本次支付的金额');
			$table->string('status', 64)->nullable();
			$table->text('details', 65535)->nullable()->comment('存储json meta 数据');
			$table->dateTime('paid_at')->nullable()->comment('支付时间');
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
		Schema::drop('ac_activity_payment_detail');
	}

}
