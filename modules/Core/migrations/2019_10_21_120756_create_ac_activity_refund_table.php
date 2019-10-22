<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcActivityRefundTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_activity_refund', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->comment('处理人');
			$table->integer('order_id')->comment('订单ID');
			$table->integer('admin_id')->unsigned()->nullable()->comment('处理人');
			$table->string('refund_no', 64)->comment('退款编号');
			$table->string('reason', 191)->nullable();
			$table->text('content', 65535)->nullable()->comment('问题描述');
			$table->integer('status')->unsigned();
			$table->integer('amount')->nullable()->default(0)->comment('金额');
			$table->dateTime('paid_time')->nullable();
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
		Schema::drop('ac_activity_refund');
	}

}
