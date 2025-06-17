<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image_url')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots_tags')->nullable();
            $table->text('custom_html_head_start')->nullable();
            $table->text('custom_html_head_end')->nullable();
            $table->text('custom_html_body_start')->nullable();
            $table->text('custom_html_body_end')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image_url')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots_tags')->nullable();
            $table->text('custom_html_head_start')->nullable();
            $table->text('custom_html_head_end')->nullable();
            $table->text('custom_html_body_start')->nullable();
            $table->text('custom_html_body_end')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image_url',
                'canonical_url',
                'robots_tags',
                'custom_html_head_start',
                'custom_html_head_end',
                'custom_html_body_start',
                'custom_html_body_end',
            ]);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image_url',
                'canonical_url',
                'robots_tags',
                'custom_html_head_start',
                'custom_html_head_end',
                'custom_html_body_start',
                'custom_html_body_end',
            ]);
        });
    }
};