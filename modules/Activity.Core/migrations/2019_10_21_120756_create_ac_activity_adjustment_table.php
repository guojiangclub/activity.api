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

class CreateAcActivityAdjustmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_activity_adjustment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id');
            $table->integer('price')->default(0);
            $table->integer('point')->default(0);
            $table->string('type');
            $table->string('label')->nullable();
            $table->string('origin_type')->nullable();
            $table->integer('origin_id')->default(0);
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
        Schema::drop('ac_activity_adjustment');
    }
}
