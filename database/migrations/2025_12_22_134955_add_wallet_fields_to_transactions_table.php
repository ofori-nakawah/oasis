<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletFieldsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add transaction_type field if it doesn't exist
            if (!Schema::hasColumn('transactions', 'transaction_type')) {
                $table->enum('transaction_type', ['topup', 'withdrawal', 'payment', 'earning', 'refund'])
                      ->default('payment')
                      ->after('payment_type');
            }
            
            // Add transaction_category field if it doesn't exist
            if (!Schema::hasColumn('transactions', 'transaction_category')) {
                $table->enum('transaction_category', ['credit', 'debit'])
                      ->nullable()
                      ->after('transaction_type');
            }
            
            // Add bank_account_details JSON field if it doesn't exist
            if (!Schema::hasColumn('transactions', 'bank_account_details')) {
                $table->json('bank_account_details')->nullable()->after('metadata');
            }
            
            // Add recipient_code field if it doesn't exist
            if (!Schema::hasColumn('transactions', 'recipient_code')) {
                $table->string('recipient_code')->nullable()->after('bank_account_details');
            }
        });
        
        // Add indexes separately to avoid issues
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'user_id') && Schema::hasColumn('transactions', 'transaction_type')) {
                try {
                    $table->index(['user_id', 'transaction_type'], 'transactions_user_id_transaction_type_index');
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
            
            if (Schema::hasColumn('transactions', 'user_id') && Schema::hasColumn('transactions', 'transaction_category')) {
                try {
                    $table->index(['user_id', 'transaction_category'], 'transactions_user_id_transaction_category_index');
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
            
            if (Schema::hasColumn('transactions', 'user_id') && Schema::hasColumn('transactions', 'status')) {
                try {
                    $table->index(['user_id', 'status'], 'transactions_user_id_status_index');
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
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
            // Drop indexes first (with error handling)
            try {
                $table->dropIndex('transactions_user_id_transaction_type_index');
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            try {
                $table->dropIndex('transactions_user_id_transaction_category_index');
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            try {
                $table->dropIndex('transactions_user_id_status_index');
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            // Drop columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('transactions', 'transaction_type')) {
                $columnsToDrop[] = 'transaction_type';
            }
            if (Schema::hasColumn('transactions', 'transaction_category')) {
                $columnsToDrop[] = 'transaction_category';
            }
            if (Schema::hasColumn('transactions', 'bank_account_details')) {
                $columnsToDrop[] = 'bank_account_details';
            }
            if (Schema::hasColumn('transactions', 'recipient_code')) {
                $columnsToDrop[] = 'recipient_code';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
}
