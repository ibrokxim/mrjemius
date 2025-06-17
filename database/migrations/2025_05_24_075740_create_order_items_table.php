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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict'); // Или set null если товар может быть удален
            $table->string('product_name'); // Сохраняем имя на момент покупки
            $table->unsignedInteger('quantity');
            $table->decimal('price_at_purchase', 10, 2); // Цена за единицу на момент покупки
            $table->decimal('total_price', 10, 2); // quantity * price_at_purchase
            $table->json('attributes')->nullable(); // Если у товара были выбраны какие-то опции
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
