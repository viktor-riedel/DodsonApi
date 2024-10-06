<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trade_me_listings', function (Blueprint $table) {
            $table->string('category_name')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('trade_me_listings', function (Blueprint $table) {
            $table->dropColumn('category_name');
        });
    }
};
