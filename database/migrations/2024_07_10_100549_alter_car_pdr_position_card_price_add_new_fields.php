<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->integer('pricing_nz_retail')->nullable();
            $table->integer('pricing_nz_wholesale')->nullable();
            $table->integer('pricing_ru_retail')->nullable();
            $table->integer('pricing_ru_wholesale')->nullable();
            $table->integer('pricing_mng_retail')->nullable();
            $table->integer('pricing_mng_wholesale')->nullable();
            $table->integer('pricing_jp_retail')->nullable();
            $table->integer('pricing_jp_wholesale')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->dropColumn('pricing_nz_retail');
            $table->dropColumn('pricing_nz_wholesale');
            $table->dropColumn('pricing_ru_retail');
            $table->dropColumn('pricing_ru_wholesale');
            $table->dropColumn('pricing_mng_retail');
            $table->dropColumn('pricing_mng_wholesale');
            $table->dropColumn('pricing_jp_retail');
            $table->dropColumn('pricing_jp_wholesale');
        });
    }
};
