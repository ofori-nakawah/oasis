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
        $skill->name = "Actor";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "App developer";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Architecture/Draftsman";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Audio engineer";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Baker";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Barber";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Barrister/Lawyer/Solicitor";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Camera/Video operations";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Carer";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Carpenter";
        $skill->save();

        $skill = new \App\Models\Skill();
        $skill->name = "Chef/Caterer";
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
