<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFixedTermJobFieldsToPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string("title")->nullable();
            $table->text("qualifications")->nullable();
            $table->text("tags")->nullable();
            $table->string("employer")->nullable();
            $table->string("start_date")->nullable();
            $table->string("end_date")->nullable();
            $table->string("is_renewable")->default("no");
            $table->string("is_internship")->default("no");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn("title");
            $table->dropColumn("qualifications");
            $table->dropColumn("employer");
            $table->dropColumn("start_date");
            $table->dropColumn("end_date");
            $table->dropColumn("is_renewable");
            $table->dropColumn("is_internship");
            $table->dropColumn("tags");
        });
    }
}
