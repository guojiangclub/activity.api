<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcActivityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_activity', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->default(0)->comment('前端发布活动的用户id');
			$table->string('title');
			$table->string('subtitle')->nullable();
			$table->string('share_title')->nullable()->comment('分享语');
			$table->text('content')->nullable();
			$table->integer('city_id')->nullable();
			$table->string('img')->nullable();
			$table->string('img_list')->nullable();
			$table->string('address')->nullable();
			$table->string('address_name')->nullable();
			$table->string('address_point')->nullable();
			$table->integer('member_limit')->nullable()->comment('活动限制报名人数');
			$table->integer('member_count')->nullable()->default(0)->comment('活动报名人数');
			$table->integer('like_count')->nullable()->default(0)->comment('活动喜欢人数');
			$table->integer('difficult')->nullable()->default(0)->comment('活动难度 0-5');
			$table->dateTime('published_at')->nullable();
			$table->dateTime('starts_at')->nullable();
			$table->dateTime('ends_at')->nullable();
			$table->dateTime('entry_end_at')->nullable();
			$table->integer('finish_min_hours')->default(0);
			$table->integer('finish_min_minutes')->default(0);
			$table->integer('finish_max_hours')->default(0);
			$table->integer('finish_max_minutes')->default(0);
			$table->boolean('refund_status')->default(0)->comment('是否支持退款');
			$table->integer('refund_term')->nullable();
			$table->string('refund_text')->nullable();
			$table->integer('status')->nullable()->default(0);
			$table->string('type')->nullable()->default('TRAIN');
			$table->string('fee_type')->nullable();
			$table->integer('delay_sign')->nullable()->default(0);
			$table->integer('statement_id')->nullable()->comment('免责声明');
			$table->integer('category_id')->nullable()->comment('活动分类');
			$table->integer('form_id')->nullable()->comment('报名表单id');
			$table->boolean('send_message')->default(0)->comment('报名成功短信通知');
			$table->string('package_get_address', 191)->nullable()->comment('参赛包领取地址');
			$table->dateTime('package_get_time')->nullable()->comment('参赛包领取时间');
			$table->text('description', 65535)->nullable()->comment('活动简介');
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
		Schema::drop('ac_activity');
	}

}
