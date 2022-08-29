<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMachineStatusTableRenameCounterNumberToProductionCounter2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_status', function (Blueprint $table) {
            $table->renameColumn('counter_number', 'production_counter2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_status', function (Blueprint $table) {
            //
        });
    }
}
