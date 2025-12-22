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
        // SQLite doesn't support MODIFY COLUMN syntax
        // For SQLite, the column is already nullable (from table creation) or we skip this
        if (Schema::hasColumn('transactions', 'pay_link_url') && \DB::getDriverName() !== 'sqlite') {
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
        // SQLite doesn't support MODIFY COLUMN syntax
        if (Schema::hasColumn('transactions', 'pay_link_url') && \DB::getDriverName() !== 'sqlite') {
            // First, set any null values to empty string
            \DB::table('transactions')
                ->whereNull('pay_link_url')
                ->update(['pay_link_url' => '']);
            
            // Revert pay_link_url to not nullable
            \DB::statement('ALTER TABLE `transactions` MODIFY COLUMN `pay_link_url` VARCHAR(255) NOT NULL');
        }
    }
}
