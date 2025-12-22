<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePayLinkUrlNullableInTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid requiring Doctrine DBAL
        if (Schema::hasColumn('transactions', 'pay_link_url')) {
            \DB::statement('ALTER TABLE `transactions` MODIFY COLUMN `pay_link_url` VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('transactions', 'pay_link_url')) {
            // First, set any null values to empty string
            \DB::table('transactions')
                ->whereNull('pay_link_url')
                ->update(['pay_link_url' => '']);
            
            // Revert pay_link_url to not nullable
            \DB::statement('ALTER TABLE `transactions` MODIFY COLUMN `pay_link_url` VARCHAR(255) NOT NULL');
        }
    }
}
