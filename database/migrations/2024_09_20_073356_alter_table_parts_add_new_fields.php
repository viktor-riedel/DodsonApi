<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->integer('actual_price_nzd')->nullable()->after('price_nzd');
            $table->integer('standard_price_nzd')->nullable()->after('price_nzd');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('actual_price_nzd');
            $table->dropColumn('standard_price_nzd');
        });
    }
};
