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
        Schema::create('refah_users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('national_code')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->integer('number_of_family_members')->default(0);
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('job')->nullable();
            $table->decimal('income', 20, 2)->nullable();
            $table->foreignId('refah_cart_id')->nullable()->constrained('refah_cart');
            $table->enum('how_to_receive', ['in_person', 'mail_to_address'])->default('in_person'); 
            $table->enum('payment_method', ['cash', 'card', 'online', 'installment'])->default('cash');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refah_users');
    }
};
