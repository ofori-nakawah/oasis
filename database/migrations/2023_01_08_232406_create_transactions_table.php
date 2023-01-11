<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string("client_reference");
            $table->string("amount");
            $table->string("email");
            $table->string("pay_link_url");
            $table->string("response_code")->nullable();
            $table->string("response_description")->nullable();
            $table->string("payment_type")->nullable();
            $table->string("charges")->nullable();
            $table->string("amount_after_charges")->nullable();
            $table->string("status");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
