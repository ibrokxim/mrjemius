<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('is_featured');

            $table->string('ikpu_code')->nullable()->after('sku');
            $table->string('package_code')->nullable()->after('ikpu_code');
            $table->string('units_code')->nullable()->after('package_code');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'ikpu_code', 'package_code', 'units_code']);
        });
    }
};
