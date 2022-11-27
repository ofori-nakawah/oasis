<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("user_id");
            $table->string("name");
            $table->string("description");
            $table->string("date");
            $table->string("time");
            $table->string("location");
            $table->string("type");
            $table->string("coords");
            $table->string("maximum_number_of_volunteers")->nullable();
            $table->string("volunteer_hours")->nullable();
            $table->string("other_relevant_information")->nullable();
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
        Schema::dropIfExists('posts');
    }
}
