<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDowntimeReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downtime_reasons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reason_group_id');
            $table->string('name');
            $table->enum('type', ['planned', 'unplanned']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('reason_group_id')
                  ->references('id')
                  ->on('downtime_reason_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('downtime_reasons');
    }
}
