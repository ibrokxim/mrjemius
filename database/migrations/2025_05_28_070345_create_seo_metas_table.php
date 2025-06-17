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
        Schema::create('seo_metas', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('locale')->nullable()->index();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->string('canonical_url')->nullable();
            $table->string('robots_tags')->nullable();

            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image_url')->nullable();
            // Поля для добавления произвольного HTML
            $table->text('custom_html_head_start')->nullable();
            $table->text('custom_html_head_end')->nullable();
            $table->text('custom_html_body_start')->nullable();
            $table->text('custom_html_body_end')->nullable();

            $table->timestamps();
            $table->unique(['model_type', 'model_id', 'locale'], 'seo_meta_model_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metas');
    }
};
