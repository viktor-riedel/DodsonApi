<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_finances', function (Blueprint $table) {
            $table->renameColumn('price_with_engine', 'price_with_engine_nz');
            $table->renameColumn('price_without_engine', 'price_without_engine_nz');
            $table->integer('price_without_engine_ru')->nullable();
            $table->integer('price_with_engine_ru')->nullable();
            $table->integer('price_with_engine_mn')->nullable();
            $table->integer('price_without_engine_mn')->nullable();
            $table->integer('price_with_engine_jp')->nullable();
            $table->integer('price_without_engine_jp')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('car_finances', function (Blueprint $table) {
            $table->renameColumn('price_with_engine_nz', 'price_with_engine');
            $table->renameColumn('price_without_engine_nz', 'price_without_engine');
            $table->dropColumn('price_without_engine_ru');
            $table->dropColumn('price_with_engine_ru');
            $table->dropColumn('price_with_engine_mn');
            $table->dropColumn('price_without_engine_mn');
            $table->dropColumn('price_with_engine_jp');
            $table->dropColumn('price_without_engine_jp');
        });
    }
};
