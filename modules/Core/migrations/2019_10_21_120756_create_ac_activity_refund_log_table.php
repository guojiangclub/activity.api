<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcActivityRefundLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_activity_refund_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('refund_id');
			$table->integer('user_id');
			$table->integer('admin_id')->nullable()->default(0);
			$table->string('action', 64);
			$table->string('note')->nullable();
			$table->text('remark', 65535)->nullable();
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
		Schema::drop('ac_activity_refund_log');
	}

}
