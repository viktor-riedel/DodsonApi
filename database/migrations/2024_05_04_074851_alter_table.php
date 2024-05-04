<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->integer('price_mng_wholesale')->nullable()->after('price_ru_retail');
            $table->integer('price_mng_retail')->nullable()->after('price_ru_retail');
            $table->integer('price_jp_retail')->nullable()->after('price_ru_retail');
            $table->integer('price_jp_wholesale')->nullable()->after('price_ru_retail');
            $table->integer('nz_team_price')->nullable()->after('price_ru_retail');
            $table->integer('nz_team_needs')->nullable()->after('price_ru_retail');
            $table->integer('nz_needs')->nullable()->after('price_ru_retail');
            $table->integer('ru_needs')->nullable()->after('price_ru_retail');
            $table->integer('jp_needs')->nullable()->after('price_ru_retail');
            $table->integer('mng_needs')->nullable()->after('price_ru_retail');
            $table->integer('needs')->nullable()->after('price_ru_retail');
        });
    }

    public function down(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->dropColumn('price_mng_wholesale');
            $table->dropColumn('price_mng_retail');
            $table->dropColumn('price_jp_retail');
            $table->dropColumn('price_jp_wholesale');
            $table->dropColumn('nz_team_price');
            $table->dropColumn('nz_team_needs');
            $table->dropColumn('nz_needs');
            $table->dropColumn('ru_needs');
            $table->dropColumn('jp_needs');
            $table->dropColumn('mng_needs');
            $table->dropColumn('needs');
        });
    }
};
