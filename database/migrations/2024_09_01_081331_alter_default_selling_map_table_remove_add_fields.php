<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('selling_map_items', function (Blueprint $table) {
            $table->dropColumn('price_jpy');
            $table->dropColumn('price_rub');
            $table->dropColumn('price_nzd');
            $table->dropColumn('price_mng');

            $table->integer('price_a_jpy')->nullable();
            $table->integer('price_b_jpy')->nullable();
            $table->integer('price_c_jpy')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('selling_map_items', function (Blueprint $table) {
            $table->integer('price_jpy')->nullable();
            $table->integer('price_rub')->nullable();
            $table->integer('price_nzd')->nullable();
            $table->integer('price_mng')->nullable();

            $table->dropColumn('price_a_jpy');
            $table->dropColumn('price_b_jpy');
            $table->dropColumn('price_c_jpy');
        });
    }
};
