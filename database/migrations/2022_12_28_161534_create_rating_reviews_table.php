<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_reviews', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("user_id");
            $table->string("job_application_id");
            $table->string("post_id");
            $table->string("rating");
            $table->string("expertise_rating");
            $table->string("work_ethic_rating");
            $table->string("professionalism_rating");
            $table->string("customer_service_rating");
            $table->text("feedback_message")->nullable();
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
        Schema::dropIfExists('rating_reviews');
    }
}
