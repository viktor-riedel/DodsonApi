<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->integer('minimum_threshold_jp_retail')->nullable();
            $table->integer('minimum_threshold_jp_wholesale')->nullable();
            $table->integer('minimum_threshold_mng_retail')->nullable();
            $table->integer('minimum_threshold_mng_wholesale')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('car_pdr_position_card_prices', function (Blueprint $table) {
            $table->dropColumn('minimum_threshold_jp_retail');
            $table->dropColumn('minimum_threshold_jp_wholesale');
            $table->dropColumn('minimum_threshold_mng_retail');
            $table->dropColumn('minimum_threshold_mng_wholesale');
        });
    }
};
