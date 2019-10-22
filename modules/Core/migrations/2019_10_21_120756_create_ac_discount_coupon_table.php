<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcDiscountCouponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_discount_coupon', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('discount_id');
			$table->integer('user_id')->unsigned();
			$table->string('code')->nullable();
			$table->dateTime('used_at')->nullable();
			$table->dateTime('expires_at')->nullable();
			$table->string('channel', 191)->default('ec');
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
		Schema::drop('ac_discount_coupon');
	}

}
