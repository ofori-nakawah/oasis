<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnhanceTransactionsTableForPaystack extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add id if it doesn't exist
            // SQLite doesn't support adding PRIMARY KEY columns to existing tables
            if (!Schema::hasColumn('transactions', 'id') && DB::getDriverName() !== 'sqlite') {
                $table->id()->first();
            }
            
            // Add user_id foreign key if it doesn't exist
            if (!Schema::hasColumn('transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
            
            // Add uuid for external references
            if (!Schema::hasColumn('transactions', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
            
            // Add Paystack-specific fields (only if they don't exist)
            if (!Schema::hasColumn('transactions', 'paystack_reference')) {
                $table->string('paystack_reference')->nullable()->unique()->after('client_reference');
            }
            
            // Add currency field
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->string('currency', 3)->default('GHS')->after('amount');
            }
            
            // Add authorization_url (keep pay_link_url if it exists for backward compatibility)
            if (!Schema::hasColumn('transactions', 'authorization_url')) {
                $table->string('authorization_url')->nullable()->after('email');
            }
            
            // Add access_code
            if (!Schema::hasColumn('transactions', 'access_code')) {
                $table->string('access_code')->nullable()->after('authorization_url');
            }
            
            // Add gateway_response (keep response_description if it exists)
            if (!Schema::hasColumn('transactions', 'gateway_response')) {
                $table->string('gateway_response')->nullable()->after('status');
            }
            
            // Add channel
            if (!Schema::hasColumn('transactions', 'channel')) {
                $table->string('channel')->nullable()->after('gateway_response');
            }
            
            // Add paid_at timestamp
            if (!Schema::hasColumn('transactions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('channel');
            }
            
            // Add metadata JSON field
            if (!Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('paid_at');
            }
            
            // Add customer_data JSON field
            if (!Schema::hasColumn('transactions', 'customer_data')) {
                $table->json('customer_data')->nullable()->after('metadata');
            }
            
            // Add last_webhook_event JSON field
            if (!Schema::hasColumn('transactions', 'last_webhook_event')) {
                $table->json('last_webhook_event')->nullable()->after('customer_data');
            }
            
            // Add soft deletes if not exists
            if (!Schema::hasColumn('transactions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        // Add indexes separately to avoid issues
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'paystack_reference')) {
                $table->index('paystack_reference');
            }
            if (Schema::hasColumn('transactions', 'client_reference')) {
                $table->index('client_reference');
            }
            if (Schema::hasColumn('transactions', 'user_id')) {
                $table->index('user_id');
            }
            if (Schema::hasColumn('transactions', 'status')) {
                $table->index('status');
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
            // Remove indexes
            if (Schema::hasColumn('transactions', 'paystack_reference')) {
                $table->dropIndex(['paystack_reference']);
            }
            if (Schema::hasColumn('transactions', 'client_reference')) {
                $table->dropIndex(['client_reference']);
            }
            if (Schema::hasColumn('transactions', 'user_id')) {
                $table->dropIndex(['user_id']);
            }
            if (Schema::hasColumn('transactions', 'status')) {
                $table->dropIndex(['status']);
            }
            
            // Remove added columns (only if they exist)
            $columnsToDrop = [];
            if (Schema::hasColumn('transactions', 'paystack_reference')) {
                $columnsToDrop[] = 'paystack_reference';
            }
            if (Schema::hasColumn('transactions', 'currency')) {
                $columnsToDrop[] = 'currency';
            }
            if (Schema::hasColumn('transactions', 'authorization_url')) {
                $columnsToDrop[] = 'authorization_url';
            }
            if (Schema::hasColumn('transactions', 'access_code')) {
                $columnsToDrop[] = 'access_code';
            }
            if (Schema::hasColumn('transactions', 'gateway_response')) {
                $columnsToDrop[] = 'gateway_response';
            }
            if (Schema::hasColumn('transactions', 'channel')) {
                $columnsToDrop[] = 'channel';
            }
            if (Schema::hasColumn('transactions', 'paid_at')) {
                $columnsToDrop[] = 'paid_at';
            }
            if (Schema::hasColumn('transactions', 'metadata')) {
                $columnsToDrop[] = 'metadata';
            }
            if (Schema::hasColumn('transactions', 'customer_data')) {
                $columnsToDrop[] = 'customer_data';
            }
            if (Schema::hasColumn('transactions', 'last_webhook_event')) {
                $columnsToDrop[] = 'last_webhook_event';
            }
            if (Schema::hasColumn('transactions', 'deleted_at')) {
                $columnsToDrop[] = 'deleted_at';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
            
            // Drop foreign key if user_id was added
            if (Schema::hasColumn('transactions', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
        });
    }
}
