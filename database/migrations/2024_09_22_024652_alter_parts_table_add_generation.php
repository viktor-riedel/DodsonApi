<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->string('generation')->nullable()->after('year');
            $table->string('color')->nullable();
            $table->dropColumn('price_jpy');
            $table->dropColumn('price_mng');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('generation');
            $table->dropColumn('color');
            $table->integer('price_jpy')->nullable();
            $table->integer('price_mng')->nullable();
        });
    }
};
