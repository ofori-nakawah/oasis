<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->timestamps();
        });

        $skill = new \App\Models\Skill();
        $skill->name = "Carpenter";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Hairdresser";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Plumber";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Shoemaker";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Mason";
        $skill->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skills');
    }
}
