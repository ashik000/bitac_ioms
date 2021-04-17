<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_stations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('station_id');
            $table->unsignedTinyInteger('port');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('device_id')
                  ->references('id')
                  ->on('devices');

            $table->foreign('station_id')
                  ->references('id')
                  ->on('stations');

            $table->unique(['device_id', 'station_id', 'port', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_stations');
    }
}
