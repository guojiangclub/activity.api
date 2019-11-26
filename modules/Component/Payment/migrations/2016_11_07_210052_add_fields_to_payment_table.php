<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('el_payment', function (Blueprint $table) {
            $table->string('channel_no')->nullable()->after('channel');
            $table->string('pingxx_no')->nullable()->after('method_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('el_payment', function (Blueprint $table) {
            $table->dropColumn('channel_no');
            $table->dropColumn('pingxx_no');
        });

    }
}
