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
            $table->uuid("id")->primary();
            $table->string("user_id");
            $table->timestamp("start_date");
            $table->timestamp("end_date")->nullable();
            $table->string("employer");
            $table->string("role");
            $table->text("responsibilities");
            $table->text("achievements");
            $table->text("reference");
            $table->timestamp("reference_verified_at")->nullable();
            $table->timestamp("reference_verification_sent_at")->nullable();
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
