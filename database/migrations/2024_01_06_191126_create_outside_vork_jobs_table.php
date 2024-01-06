<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutsideVorkJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outside_vork_jobs', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string("start_date");
            $table->string("end_date")->nullable();
            $table->string("employer");
            $table->string("role");
            $table->text("responsibilities");
            $table->text("achievements");
            $table->text("reference");
            $table->timestamp("reference_verified_at")->nullable();
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
        Schema::dropIfExists('outside_vork_jobs');
    }
}
