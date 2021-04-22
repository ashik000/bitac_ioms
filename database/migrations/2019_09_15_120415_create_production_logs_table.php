<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->nullable(false);
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('product_id');

            $table->mediumInteger('cycle_interval');
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
        Schema::dropIfExists('production_logs');
    }
}
