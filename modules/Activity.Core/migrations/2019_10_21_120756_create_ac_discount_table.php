<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_discount', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('label')->nullable();
            $table->text('intro', 65535)->nullable();
            $table->boolean('exclusive')->default(0);
            $table->integer('usage_limit')->nullable();
            $table->integer('used')->default(0);
            $table->integer('per_usage_limit')->nullable()->default(0);
            $table->boolean('coupon_based')->default(0);
            $table->string('code')->nullable();
            $table->integer('type')->default(0);
            $table->dateTime('usestart_at')->nullable()->comment('使用开始时间');
            $table->dateTime('useend_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->integer('status')->default(1);
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
        Schema::drop('ac_discount');
    }
}
