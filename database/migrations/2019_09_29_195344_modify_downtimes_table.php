<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDowntimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('downtimes', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id')->after('duration')->nullable();
            $table->unsignedBigInteger('operator_id')->after('shift_id')->nullable();

            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts');

            $table->foreign('operator_id')
                ->references('id')
                ->on('operators');
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
