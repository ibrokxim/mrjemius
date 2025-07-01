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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('paycom_transaction_id', 25)->nullable();
            $table->string('paycom_time', 13)->nullable();
            $table->dateTime('paycom_time_datetime')->nullable();
            $table->dateTime('cancel_time')->nullable();
            $table->tinyInteger('state')->nullable();
            $table->string('owner_id')->nullable();
            $table->tinyInteger('reason')->nullable();
            $table->string('receivers')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('perform_time_unix', 13)->nullable();
            $table->string('transaction')->nullable();
            $table->string('code')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->string('payme_time')->nullable();
            $table->string('create_time')->nullable();
            $table->string('perform_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
