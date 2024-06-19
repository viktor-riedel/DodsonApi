<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('without_engine');
            $table->dropColumn('price_with_engine');
            $table->dropColumn('price_without_engine');
            $table->string('item_name_eng')->nullable()->after('with_engine');
            $table->string('item_name_ru')->nullable()->after('with_engine');
            $table->integer('price_jpy')->default(0)->after('with_engine');
            $table->integer('engine_price')->default(0)->after('with_engine');
            $table->integer('catalyst_price')->default(0)->after('with_engine');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('item_name_eng');
            $table->dropColumn('item_name_ru');
            $table->dropColumn('price_jpy');
            $table->integer('without_engine')->nullable();
            $table->integer('price_with_engine')->nullable();
            $table->integer('price_without_engine')->nullable();
        });
    }
};
