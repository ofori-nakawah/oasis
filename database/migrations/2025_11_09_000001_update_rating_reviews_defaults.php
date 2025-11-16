<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateRatingReviewsDefaults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('rating_reviews')) {
            // Backfill existing records: set 0/blank/null to 5 for all rating dimensions
            DB::table('rating_reviews')
                ->whereIn('rating', ['0', 0, null, ''])
                ->update(['rating' => 5]);

            DB::table('rating_reviews')
                ->whereIn('expertise_rating', ['0', 0, null, ''])
                ->update(['expertise_rating' => 5]);

            DB::table('rating_reviews')
                ->whereIn('work_ethic_rating', ['0', 0, null, ''])
                ->update(['work_ethic_rating' => 5]);

            DB::table('rating_reviews')
                ->whereIn('professionalism_rating', ['0', 0, null, ''])
                ->update(['professionalism_rating' => 5]);

            DB::table('rating_reviews')
                ->whereIn('customer_service_rating', ['0', 0, null, ''])
                ->update(['customer_service_rating' => 5]);

            // Set column defaults for new rows
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN rating SET DEFAULT '5'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN expertise_rating SET DEFAULT '5'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN work_ethic_rating SET DEFAULT '5'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN professionalism_rating SET DEFAULT '5'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN customer_service_rating SET DEFAULT '5'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('rating_reviews')) {
            // Restore defaults back to 0 (original behaviour / no default equivalent)
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN rating SET DEFAULT '0'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN expertise_rating SET DEFAULT '0'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN work_ethic_rating SET DEFAULT '0'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN professionalism_rating SET DEFAULT '0'");
            DB::statement("ALTER TABLE rating_reviews ALTER COLUMN customer_service_rating SET DEFAULT '0'");
        }
    }
}


