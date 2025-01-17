<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->enum('tag', ['hourly', 'daily', 'weekly', 'monthly', 'yearly'])->index();
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');

            $table->dateTime('generated_at');
            $table->unsignedBigInteger('expected');
            $table->unsignedBigInteger('produced');
            $table->unsignedBigInteger('scraped');
            $table->unsignedBigInteger('available');
            $table->unsignedBigInteger('unplanned_downtime');
            $table->unsignedBigInteger('planned_downtime');
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
        Schema::dropIfExists('reports');
    }
}
