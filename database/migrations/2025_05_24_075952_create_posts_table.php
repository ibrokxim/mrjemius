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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('Author')->constrained('users')->onDelete('cascade');
            // $table->foreignId('post_category_id')->nullable()->constrained('post_categories')->onDelete('set null'); // Если одна категория на пост
            $table->text('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); // Краткое описание
            $table->longText('content');
            $table->string('featured_image_url')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
