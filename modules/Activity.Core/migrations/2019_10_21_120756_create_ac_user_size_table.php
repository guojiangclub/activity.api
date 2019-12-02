<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElUserSizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_user_size', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('upper')->nullable();
            $table->string('lower')->nullable();
            $table->string('shoes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ac_user_size');
    }
}
