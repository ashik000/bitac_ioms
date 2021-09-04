<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThresholdPercentageColumnStationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_products', function (Blueprint $table) {
            $table->integer('threshold_percentage')->after('cycle_time')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('station_products', function (Blueprint $table) {
            $table->dropColumn('threshold_percentage');
        });
    }
}
