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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_category_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 4)->unique();
            $table->string('fabric_color')->nullable();
            $table->json('measurements')->nullable();
            $table->string('design_image')->nullable();
            $table->decimal('total_price', 8, 2)->default(0);
            $table->decimal('deposit', 8, 2)->default(0);
            $table->boolean('is_fully_paid')->default(false);
            $table->string('status')->default('pending');
            $table->date('delivery_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
