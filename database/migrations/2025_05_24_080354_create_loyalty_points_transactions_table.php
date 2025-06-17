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
        Schema::create('loyalty_points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // Связь с заказом, если транзакция по нему
            $table->enum('type', ['earned', 'spent', 'refunded', 'expired', 'manual_adjustment'])->comment('Тип транзакции');
            $table->integer('points'); // Количество баллов (может быть отрицательным для списания)
            $table->text('description')->nullable(); // Описание (например, "Начислено за заказ #123", "Списано на скидку")
            $table->timestamp('expires_at')->nullable(); // Если баллы имеют срок годности
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points_transactions');
    }
};
