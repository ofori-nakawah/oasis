<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->timestamps();
        });

        $skill = new \App\Models\Language();
        $skill->name = "Adangme";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "English";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Ewe";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Fante";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Ga";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Twi";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Gonja";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Hausa";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Dagbani";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "French";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Hindi";
        $skill->save();

        $skill = new \App\Models\Language();
        $skill->name = "Dutch";
        $skill->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
