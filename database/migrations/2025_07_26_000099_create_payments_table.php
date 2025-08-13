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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('method', ['online', 'cash', 'bank_transfer']);
            $table->enum('status', ['pending', 'completed', 'failed']);
            $table->dateTime('paid_at')->nullable();
            $table->decimal('amount', 20, 2);
            $table->string('merchant_id')->nullable();
            $table->string('bank_transaction_id')->nullable();
            $table->string('bank_reference_id')->nullable();
            $table->string('description')->nullable();
            $table->string('callback_url')->nullable();
            $table->text('gateway_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}; 