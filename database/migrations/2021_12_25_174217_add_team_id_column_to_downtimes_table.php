<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdColumnToDowntimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('downtimes', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->after('operator_id')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('downtimes', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
}
