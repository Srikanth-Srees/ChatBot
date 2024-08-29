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
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('in_pin_code')->nullable();
            $table->string('building_name')->nullable();
            $table->string('landmark_area')->nullable();
            $table->string('address')->nullable();
            $table->string('tower_number')->nullable();
            $table->string('city')->nullable();
            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('house_number')->nullable();
            $table->string('floor_number')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_addresses');
    }
};
