<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->integer('delivery_price_nz')->nullable();
            $table->integer('delivery_price_ru')->nullable();
            $table->integer('pinnacle_price')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->dropColumn(['delivery_price_nz', 'delivery_price_ru', 'pinnacle_price']);
        });
    }
};
