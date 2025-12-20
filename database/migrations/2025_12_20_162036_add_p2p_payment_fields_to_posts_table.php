<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddP2pPaymentFieldsToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Initial payment fields (paid at quote approval)
            if (!Schema::hasColumn('posts', 'initial_payment_amount')) {
                $table->string('initial_payment_amount')->nullable()->after('final_payment_amount');
            }
            if (!Schema::hasColumn('posts', 'initial_payment_paid_at')) {
                $table->timestamp('initial_payment_paid_at')->nullable()->after('initial_payment_amount');
            }
            if (!Schema::hasColumn('posts', 'initial_payment_transaction_id')) {
                $table->unsignedBigInteger('initial_payment_transaction_id')->nullable()->after('initial_payment_paid_at');
                $table->foreign('initial_payment_transaction_id')->references('id')->on('transactions')->onDelete('set null');
            }
            
            // Final payment fields (paid at job completion)
            // Note: final_payment_amount already exists, we're adding tracking fields
            if (!Schema::hasColumn('posts', 'final_payment_paid_at')) {
                $table->timestamp('final_payment_paid_at')->nullable()->after('final_payment_amount');
            }
            if (!Schema::hasColumn('posts', 'final_payment_transaction_id')) {
                $table->unsignedBigInteger('final_payment_transaction_id')->nullable()->after('final_payment_paid_at');
                $table->foreign('final_payment_transaction_id')->references('id')->on('transactions')->onDelete('set null');
            }
            
            // Payment status tracking
            if (!Schema::hasColumn('posts', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('final_payment_transaction_id')
                    ->comment('pending, initial_paid, fully_paid');
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
        Schema::table('posts', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('posts', 'initial_payment_transaction_id')) {
                $table->dropForeign(['initial_payment_transaction_id']);
            }
            if (Schema::hasColumn('posts', 'final_payment_transaction_id')) {
                $table->dropForeign(['final_payment_transaction_id']);
            }
            
            // Drop columns
            $columnsToDrop = [];
            if (Schema::hasColumn('posts', 'initial_payment_amount')) {
                $columnsToDrop[] = 'initial_payment_amount';
            }
            if (Schema::hasColumn('posts', 'initial_payment_paid_at')) {
                $columnsToDrop[] = 'initial_payment_paid_at';
            }
            if (Schema::hasColumn('posts', 'initial_payment_transaction_id')) {
                $columnsToDrop[] = 'initial_payment_transaction_id';
            }
            if (Schema::hasColumn('posts', 'final_payment_paid_at')) {
                $columnsToDrop[] = 'final_payment_paid_at';
            }
            if (Schema::hasColumn('posts', 'final_payment_transaction_id')) {
                $columnsToDrop[] = 'final_payment_transaction_id';
            }
            if (Schema::hasColumn('posts', 'payment_status')) {
                $columnsToDrop[] = 'payment_status';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
}
