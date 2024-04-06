<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_pdr_position_card_id');
            $table->integer('price_nz_wholesale')->nullable();
            $table->integer('price_nz_retail')->nullable();
            $table->integer('price_ru_wholesale')->nullable();
            $table->integer('price_ru_retail')->nullable();
            $table->integer('price_jp_minimum_buy')->nullable();
            $table->integer('price_jp_maximum_buy')->nullable();
            $table->integer('minimum_threshold_nz_retail')->nullable();
            $table->integer('minimum_threshold_nz_wholesale')->nullable();
            $table->integer('minimum_threshold_ru_retail')->nullable();
            $table->integer('minimum_threshold_ru_wholesale')->nullable();
            $table->integer('delivery_price_nz')->nullable();
            $table->integer('delivery_price_ru')->nullable();
            $table->integer('pinnacle_price')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('car_pdr_position_card_id')
                ->references('id')
                ->on('car_pdr_position_cards')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_pdr_position_card_prices');
    }
};
