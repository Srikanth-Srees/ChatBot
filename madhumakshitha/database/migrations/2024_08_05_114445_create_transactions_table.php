<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('transactionId')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->string('card_type')->nullable();
            $table->string('bank_transaction_id')->nullable();
            $table->string('bank_id')->nullable();
            $table->json('complete_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
