<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_modifications', function (Blueprint $table) {
            $table->integer('gen_number')->nullable()->after('generation');
        });
    }

    public function down(): void
    {
        Schema::table('car_modifications', function (Blueprint $table) {
            $table->dropColumn('gen_number');
        });
    }
};
