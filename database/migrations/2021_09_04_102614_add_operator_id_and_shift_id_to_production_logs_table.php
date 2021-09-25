<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorIdAndShiftIdToProductionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('operator_id')->after('product_id')->nullable(true)->default(null);
            $table->unsignedBigInteger('shift_id')->after('operator_id')->nullable(true)->default(null);

            $table->foreign('operator_id', 'production_logs_operator_id_foreign')->references('id')->on('operators');
            $table->foreign('shift_id', 'production_logs_shift_id_foreign')->references('id')->on('shifts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_logs', function (Blueprint $table) {
            $table->dropForeign('production_logs_operator_id_foreign');
            $table->dropForeign('production_logs_shift_id_foreign');
            $table->dropColumn('operator_id');
            $table->dropColumn('shift_id');
        });
    }
}
