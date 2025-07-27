<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('article_categories')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->text('excerpt');
            $table->text('body');
            $table->boolean('is_show')->default('1');
            $table->string('image_url')->nullable();
            $table->bigInteger('view_count')->default('0');
            $table->boolean('is_future')->default(false);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('seo_follow')->default(1);
            $table->boolean('seo_index')->default(1);
            $table->string('seo_canonical')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['slug', 'is_show']);
            $table->index(['slug', 'is_show', 'deleted_at']);
            $table->index(['title']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
