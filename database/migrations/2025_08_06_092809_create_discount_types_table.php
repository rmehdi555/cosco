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
        Schema::create('discount_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained('product_categories')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('footer')->nullable();
            $table->string('background_color_up')->nullable();
            $table->string('background_color_down')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link')->nullable();
            $table->boolean('target')->nullable()->default('1');
            $table->string('ads_title')->nullable();
            $table->string('ads_footer')->nullable();
            $table->string('ads_image_url')->nullable();
            $table->string('ads_link')->nullable();
            $table->boolean('ads_target')->nullable()->default('1');
            $table->string('ads_background_color_up')->nullable();
            $table->string('ads_background_color_down')->nullable();
            $table->boolean('is_show')->default('1');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_types');
    }
};
