<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id')->nullable(true)->default(null);
            $table->text('request')->nullable(true)->default(null);
            $table->string('response', 255)->nullable(true)->default(null);
            $table->string('status', 1000)->nullable(true)->default(null);
            $table->dateTime('processing_start')->nullable(true)->default(null);
            $table->dateTime('processing_end')->nullable(true)->default(null);
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
        Schema::dropIfExists('packets');
    }
}
