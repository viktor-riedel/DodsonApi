<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_finances', function (Blueprint $table) {
            $table->integer('price_with_engine')->nullable()->after('purchase_price');
            $table->integer('price_without_engine')->nullable()->after('purchase_price');
            $table->boolean('car_is_for_sale')->default(false)->after('purchase_price');
        });
    }

    public function down(): void
    {
        Schema::table('car_finances', function (Blueprint $table) {
            $table->dropColumn('price_with_engine');
            $table->dropColumn('price_without_engine');
            $table->dropColumn('car_is_for_sale');
        });
    }
};
