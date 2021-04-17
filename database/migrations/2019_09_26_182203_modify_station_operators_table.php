<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyStationOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('station_operators', function (Blueprint $table) {
            $table->dropForeign('station_operators_user_id_foreign');
            $table->dropForeign('station_operators_station_id_foreign');
            $table->dropUnique('station_operators_station_id_user_id_deleted_at_unique');
            $table->dropColumn('user_id');

            $table->unsignedBigInteger('operator_id')->nullable(false)->after('station_id');

            $table->timestamp('start_time')->nullable(false)->after('operator_id')->useCurrent();
            $table->timestamp('end_time')->nullable(true)->after('start_time');

            $table->unique(['station_id', 'operator_id', 'end_time']);

            $table->foreign('station_id')
                ->references('id')
                ->on('stations');
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
