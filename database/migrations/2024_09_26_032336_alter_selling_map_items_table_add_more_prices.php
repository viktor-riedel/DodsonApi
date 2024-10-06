<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('selling_map_items', function (Blueprint $table) {
            $table->integer('price_d_jpy')->nullable();
            $table->integer('price_e_jpy')->nullable();
            $table->integer('price_f_jpy')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('selling_map_items', function (Blueprint $table) {
            $table->dropColumn('price_d_jpy');
            $table->dropColumn('price_e_jpy');
            $table->dropColumn('price_f_jpy');
        });
    }
};
