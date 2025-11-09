<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUserRatingDefaults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'rating')) {
            DB::table('users')
                ->whereIn('rating', ['0', 0, null, ''])
                ->update(['rating' => 5]);

            DB::statement("ALTER TABLE users ALTER COLUMN rating SET DEFAULT '5'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'rating')) {
            DB::statement("ALTER TABLE users ALTER COLUMN rating SET DEFAULT '0'");
        }
    }
}

