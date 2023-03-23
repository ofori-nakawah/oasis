<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid");
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('country_id');
            $table->string('rating')->default(0);
            $table->string('profile_picture')->nullable();
            $table->string('location_name')->nullable();
            $table->string('location_coords')->nullable();
            $table->dateTime('last_seen')->nullable();
            $table->string('fcm_token')->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('volunteer_hours')->default(0);
            $table->string('is_core_skills_set')->default(0);
            $table->string('is_languages_set')->default(0);
            $table->string('is_verified')->default(0);
            $table->string('total_earnings')->default(0);
            $table->string('total_topups')->default(0);
            $table->string('available_balance')->default(0);
            $table->string('total_payouts')->default(0);
            $table->integer('type')->default(1); // 0 is admin | 1 is app user
            $table->integer('status')->default(0); // 0 is onboarding | 1 is active | 2 is blocked
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
