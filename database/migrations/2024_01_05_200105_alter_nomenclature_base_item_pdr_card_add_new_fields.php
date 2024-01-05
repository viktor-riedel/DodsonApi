<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->integer('minimum_threshold_nz_retail')->nullable();
            $table->integer('minimum_threshold_nz_wholesale')->nullable();
            $table->integer('minimum_threshold_ru_retail')->nullable();
            $table->integer('minimum_threshold_ru_wholesale')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->dropColumn('minimum_threshold_nz_retail');
            $table->dropColumn('minimum_threshold_nz_wholesale');
            $table->dropColumn('minimum_threshold_ru_retail');
            $table->dropColumn('minimum_threshold_ru_wholesale');
        });
    }
};
