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
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('item_count')->default(0);
            $table->enum('status', ['pending', 'picked', 'packed', 'shipped', 'delivered'])->default('pending');
            $table->text('shipping_address');
            $table->string('tracking_number')->nullable();
            $table->string('pallet_number')->nullable();
            $table->text('notes')->nullable();
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
