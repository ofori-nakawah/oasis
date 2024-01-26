<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->timestamps();
        });

        $industries = ["Aerospace",
            "Agricultural ",
            "Automotive",
            "Basic metal ",
            "Chemical ",
            "Computer",
            "Construction ",
            "Creative ",
            "Cultural",
            "Defense",
            "Education",
            "Electric power",
            "Electronics",
            "Energy",
            "Engineering ",
            "Entertainment",
            "Farming",
            "Fashion",
            "Film",
            "Financial services",
            "Fishing",
            "Food",
            "Forestry",
            "Gambling",
            "Gas",
            "Green",
            "Health services ",
            "Hospitality",
            "Information",
            "Information technology",
            "Infrastructure",
            "Insurance ",
            "Leisure",
            "Low technology",
            "Manufacturing",
            "Meat",
            "Media",
            "Merchandising",
            "Mining ",
            "Music",
            "News media ",
            "Oil & Gas",
            "Pharmaceutical",
            "Professional",
            "Publishing",
            "Pulp and paper",
            "Railway ",
            "Real estate ",
            "Retail ",
            "Scientific",
            "Services",
            "Software",
            "Sport",
            "Steel",
            "Technology",
            "Telecommunications",
            "Textile",
            "Tobacco",
            "Transport",
            "Utilities",
            "Wholesale"];

        try {
            foreach ($industries as $industry) {
                $industryDefaultRecord = new \App\Models\Industry();
                $industryDefaultRecord->name = $industry;
                $industryDefaultRecord->save();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::debug("ERROR CREATED DEFAULT INDUSTRY RECORD >>>>>>>>>>>>>>>>>> " . $e);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('industries');
    }
}
