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
        Schema::create('customerproducts__details', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile');
            $table->string('amount');
            $table->json('ordered_products');
            $table->unsignedBigInteger('delivery_detail_id')->nullable(); 
            $table->string('delivery_detail_status');
            $table->unsignedBigInteger('transaction_detail_id')->nullable(); 
            $table->string('transaction_detail_status');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('delivery_detail_id')
                ->references('id')
                ->on('delivery_addresses') // This is the table name
                ->onDelete('set null');

            $table->foreign('transaction_detail_id')
                ->references('id')
                ->on('transactions') // This is the table name
                ->onDelete('set null'); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerproducts__details');
    }
};
