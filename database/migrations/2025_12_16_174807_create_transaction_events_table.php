<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // charge.success, charge.failed, transfer.success, etc.
            $table->string('paystack_event_id')->unique(); // Paystack event ID for idempotency
            $table->json('payload'); // Full webhook payload
            $table->boolean('processed')->default(false); // Whether event was processed
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable(); // Error if processing failed
            $table->timestamps();
            
            $table->index('transaction_id');
            $table->index('paystack_event_id');
            $table->index('event_type');
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_events');
    }
}
