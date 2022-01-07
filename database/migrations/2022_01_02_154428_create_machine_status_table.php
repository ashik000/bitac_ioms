<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_status', function (Blueprint $table) {

            $table->bigIncrements('id')->nullable(false);
            $table->unsignedBigInteger('station_id');

            $table->string('spindle_speed');
            $table->string('feed_rate');
            $table->string('machine_uptime');
            $table->string('alarm_code');
            $table->string('alarm_info');
            $table->string('program_number');
            $table->string('program_name');
            $table->string('cycle_time');
            $table->string('counter_number');
            $table->string('power_status');



            $table->dateTime('produced_at');
            $table->dateTime('synced_at');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_status');
    }
}
