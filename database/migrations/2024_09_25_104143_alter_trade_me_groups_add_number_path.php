<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trade_me_groups', function (Blueprint $table) {
            $table->string('number_path')->after('trade_me_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trade_me_groups', function (Blueprint $table) {
            $table->dropColumn('number_path');
        });
    }
};
