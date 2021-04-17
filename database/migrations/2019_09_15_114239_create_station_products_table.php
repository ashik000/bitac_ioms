<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedMediumInteger('cycle_time'); // STORE AS **SEC_PER_PCS**
            $table->enum('cycle_unit', ['SEC_PER_PCS', 'PCS_PER_SEC', 'PCS_PER_MINUTE', 'PCS_PER_HOUR']);
            $table->unsignedMediumInteger('cycle_timeout');
            $table->unsignedMediumInteger('units_per_signal');
            $table->unsignedTinyInteger('performance_threshold');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('station_id')
                  ->references('id')
                  ->on('stations');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products');

            $table->unique(['station_id', 'product_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('station_products');
    }
}
