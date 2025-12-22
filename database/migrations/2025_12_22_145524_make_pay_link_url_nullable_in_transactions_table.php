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
        Schema::table('transactions', function (Blueprint $table) {
            // Make pay_link_url nullable since earning transactions don't have payment links
            if (Schema::hasColumn('transactions', 'pay_link_url')) {
                $table->string('pay_link_url')->nullable()->change();
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
            // Revert pay_link_url to not nullable (but this might fail if there are null values)
            if (Schema::hasColumn('transactions', 'pay_link_url')) {
                // First, set any null values to empty string
                \DB::table('transactions')
                    ->whereNull('pay_link_url')
                    ->update(['pay_link_url' => '']);
                
                $table->string('pay_link_url')->nullable(false)->change();
            }
        });
    }
}
