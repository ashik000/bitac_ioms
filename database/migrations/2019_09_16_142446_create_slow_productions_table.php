<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlowProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slow_productions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('production_log_id');
            $table->dateTime('start_time');
            $table->unsignedMediumInteger('duration');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('production_log_id')
                  ->references('id')
                  ->on('production_logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slow_productions');
    }
}
