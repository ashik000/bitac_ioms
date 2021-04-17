<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDowntimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downtimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('production_log_id');
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->dateTime('start_time');
            $table->unsignedMediumInteger('duration');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('production_log_id')
                  ->references('id')
                  ->on('production_logs');

            $table->foreign('reason_id')
                  ->references('id')
                  ->on('downtime_reasons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('downtimes');
    }
}
