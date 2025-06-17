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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Поля для Telegram аутентификации
            $table->bigInteger('telegram_id')->unique()->nullable();
            $table->string('telegram_username')->nullable();
            $table->string('telegram_first_name')->nullable();
            $table->string('telegram_last_name')->nullable();
            $table->string('telegram_photo_url')->nullable();

            // Основные поля пользователя
            $table->string('name');
            $table->string('email')->unique()->nullable(); // Nullable для возможности регистрации только через Telegram
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable для возможности регистрации только через Telegram
            $table->string('phone_number')->nullable(); // Добавим, полезное поле

            // Поля для программы лояльности
            $table->unsignedBigInteger('loyalty_points')->default(0);
            $table->string('loyalty_card_number')->nullable()->unique();

            // Стандартные поля Laravel
            $table->rememberToken();
            $table->timestamps(); // created_at и updated_at
            $table->softDeletes(); // deleted_at, если планируете использовать "мягкое удаление"

            // Дополнительное поле для роли (простой вариант)
            $table->boolean('is_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
