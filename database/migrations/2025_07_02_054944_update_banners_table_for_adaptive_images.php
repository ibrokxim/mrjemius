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
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('banner_image_url');
            $table->string('desktop_image_url');
            $table->string('mobile_image_url');
            $table->string('link_url')->nullable(); // Добавляем поле для ссылки

            $table->string('desktop_bg_position')->default('center center');
            $table->string('mobile_bg_position')->default('center center');

            $table->integer('desktop_height')->default(400);
            $table->integer('mobile_height')->default(350);
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // Логика для отката миграции (возвращаем все как было)
            $table->string('banner_image_url')->nullable(); // Возвращаем старый столбец

            $table->dropColumn([
                'desktop_image_url',
                'mobile_image_url',
                'link_url',
                'desktop_bg_position',
                'mobile_bg_position',
                'desktop_height',
                'mobile_height'
            ]);
        });
    }
};
