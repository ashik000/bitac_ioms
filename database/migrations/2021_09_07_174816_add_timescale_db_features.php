<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTimescaleDbFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE EXTENSION IF NOT EXISTS timescaledb");
        DB::statement("SELECT create_hypertable('production_logs', 'produced_at')");
        DB::statement("SELECT create_hypertable('downtimes', 'start_time')");
        DB::statement("SELECT create_hypertable('slow_productions', 'start_time')");
        DB::statement("SELECT create_hypertable('reports', 'generated_at')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
