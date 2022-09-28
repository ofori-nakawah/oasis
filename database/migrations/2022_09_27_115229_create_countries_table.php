<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("country_code");
            $table->text("img")->nullable();
            $table->boolean("is_phone_number_required_during_onboarding")->default(false);
            $table->boolean("is_phone_number_default_verification_medium")->default(false);
            $table->timestamps();
        });

        $country = new \App\Models\Country();
        $country->id = \Illuminate\Support\Str::uuid();
        $country->name = "Ghana";
        $country->country_code = "GH";
        $country->is_phone_number_required_during_onboarding = true;
        $country->is_phone_number_default_verification_medium = true;
        $country->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
