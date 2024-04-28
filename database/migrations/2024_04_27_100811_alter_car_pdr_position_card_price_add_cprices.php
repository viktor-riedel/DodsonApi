<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->string('price_currency')->nullable()->after('car_pdr_position_card_id');
            $table->integer('approximate_price')->nullable()->after('car_pdr_position_card_id');
            $table->integer('real_price')->nullable()->after('car_pdr_position_card_id');
        });
    }

    public function down(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->dropColumn(['price_currency', 'approximate_price', 'real_price']);
        });
    }
};
