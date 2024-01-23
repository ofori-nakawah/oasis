<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationAndTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certification_and_trainings', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("user_id");
            $table->timestamp("start_date")->nullable();
            $table->timestamp("end_date")->nullable();
            $table->string("institution");
            $table->string("programme");
            $table->string("certificate_link")->nullable();
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
        Schema::dropIfExists('certification_and_trainings');
    }
}
