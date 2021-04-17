<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStationShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_shifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('shift_id');
            $table->string('schedule',7);
            $table->unique(['station_id', 'shift_id','deleted_at']);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('station_id')
                ->references('id')
                ->on('stations');
            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts');
        });
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
