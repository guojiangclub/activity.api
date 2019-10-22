<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcActivityFormFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_activity_form_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 64)->comment('表单名称');
			$table->integer('activity_id')->default(0)->comment('活动id');
			$table->text('fields', 65535)->nullable()->comment('表单字段');
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
		Schema::drop('ac_activity_form_fields');
	}

}
