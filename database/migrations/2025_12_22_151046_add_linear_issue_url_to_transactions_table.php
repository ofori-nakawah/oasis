<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinearIssueUrlToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'linear_issue_url')) {
                $table->text('linear_issue_url')->nullable()->after('recipient_code');
            } else {
                // If column exists but is string, change it to text
                \DB::statement('ALTER TABLE `transactions` MODIFY COLUMN `linear_issue_url` TEXT NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'linear_issue_url')) {
                $table->dropColumn('linear_issue_url');
            }
        });
    }
}
