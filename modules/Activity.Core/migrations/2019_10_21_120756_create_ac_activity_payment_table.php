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

class CreateAcActivityPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_activity_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id');
            $table->string('title');
            $table->integer('type');
            $table->integer('price')->nullable()->default(0);
            $table->integer('point')->nullable()->default(0);
            $table->integer('discount_id')->nullable()->default(0);
            $table->integer('limit');
            $table->boolean('is_limit')->default(0);
            $table->integer('status')->default(1);
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
        Schema::drop('ac_activity_payment');
    }
}
