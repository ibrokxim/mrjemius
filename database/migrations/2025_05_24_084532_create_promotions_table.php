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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed_amount', 'free_shipping'])->default('percentage');
            $table->decimal('value', 10, 2); // Сумма скидки или процент
            $table->unsignedInteger('max_uses')->nullable(); // Макс. кол-во использований всего
            $table->unsignedInteger('max_uses_user')->nullable(); // Макс. кол-во на пользователя
            $table->unsignedInteger('uses_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->decimal('minimum_spend', 10, 2)->nullable(); // Мин. сумма заказа для применения
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
