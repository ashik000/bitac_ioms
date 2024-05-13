<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToMachineStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_status', function (Blueprint $table) {
            $table->string('machining_mode')->default(null)->nullable();
            $table->string('tool_number')->default(null)->nullable();
            $table->string('tool_life')->default(null)->nullable();
            $table->string('load_on_table')->default(null)->nullable();
            $table->string('feed_rate_active')->default(null)->nullable();
            $table->string('spindle_speed_active')->default(null)->nullable();
            $table->json('maintenance_data')->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_status', function (Blueprint $table) {
            $table->dropColumn('machining_mode');
            $table->dropColumn('tool_number');
            $table->dropColumn('tool_life');
            $table->dropColumn('load_on_table');
            $table->dropColumn('feed_rate_active');
            $table->dropColumn('spindle_speed_active');
            $table->dropColumn('maintenance_data');
        });
    }
}
