<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->renameColumn('approximate_price', 'buying_price');
            $table->renameColumn('real_price', 'selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->renameColumn('buying_price', 'approximate_price');
            $table->renameColumn('selling_price', 'price');
        });
    }
};
