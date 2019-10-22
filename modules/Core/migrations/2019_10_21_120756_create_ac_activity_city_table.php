<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcActivityCityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_activity_city', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('img')->nullable();
			$table->integer('province')->nullable();
			$table->integer('city')->nullable();
			$table->integer('area')->nullable();
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
		Schema::drop('ac_activity_city');
	}

}
