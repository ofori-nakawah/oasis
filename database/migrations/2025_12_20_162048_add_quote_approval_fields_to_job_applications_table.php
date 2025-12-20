<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuoteApprovalFieldsToJobApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Quote approval tracking
            if (!Schema::hasColumn('job_applications', 'quote_approved_at')) {
                $table->timestamp('quote_approved_at')->nullable()->after('quote');
            }
            if (!Schema::hasColumn('job_applications', 'quote_approved_by')) {
                $table->string('quote_approved_by')->nullable()->after('quote_approved_at');
                // Note: Using string instead of foreign key since user_id in posts is string
                // If needed, we can add foreign key constraint later
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
        Schema::table('job_applications', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('job_applications', 'quote_approved_at')) {
                $columnsToDrop[] = 'quote_approved_at';
            }
            if (Schema::hasColumn('job_applications', 'quote_approved_by')) {
                $columnsToDrop[] = 'quote_approved_by';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
}
