<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeStationIdProductIdProducedAtColumnUniqueInProductionLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_logs', function (Blueprint $table) {
            $table->unique(['station_id', 'product_id', 'produced_at'], 'production_logs_station_id_product_id_produced_at_unique');
            $table->unsignedBigInteger('packet_id')->after('id')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_logs', function (Blueprint $table) {
            $table->dropUnique('production_logs_station_id_product_id_produced_at_unique');
            $table->dropColumn('packet_id');
        });
    }
}
