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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'paid', 'shipped', 'delivered', 'cancelled']);
            $table->decimal('total_amount', 20, 2);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded']);
            $table->foreignId('shipping_address_id')->constrained('addresses')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1000;');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
