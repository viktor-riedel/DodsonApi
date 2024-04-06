<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->integer('price_mng_retail')->nullable()->after('price_ru_wholesale');
            $table->integer('price_mng_wholesale')->nullable()->after('price_ru_wholesale');
            $table->integer('minimum_threshold_mng_retail')->nullable()->after('price_ru_wholesale');
            $table->integer('minimum_threshold_mng_wholesale')->nullable()->after('price_ru_wholesale');
            $table->integer('minimum_threshold_jp_retail')->nullable()->after('price_ru_wholesale');
            $table->integer('minimum_threshold_jp_wholesale')->nullable()->after('price_ru_wholesale');
            $table->integer('price_jp_wholesale')->nullable();
            $table->integer('price_jp_retail')->nullable();
            $table->integer('nz_needs')->nullable();
            $table->integer('ru_needs')->nullable();
            $table->integer('mng_needs')->nullable();
            $table->integer('jp_needs')->nullable();
            $table->integer('needs')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->dropColumn([
                'price_mng_retail', 'price_mng_wholesale', 'minimum_threshold_mng_retail',
                'minimum_threshold_mng_wholesale', 'nz_needs', 'ru_needs', 'mng_needs', 'jp_needs',
                'price_jp_wholesale', 'price_jp_retail',
                'minimum_threshold_jp_retail', 'minimum_threshold_jp_wholesale', 'needs',
            ]);
        });
    }
};
