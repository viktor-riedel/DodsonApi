<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_cards', function (Blueprint $table) {
            $table->string('color')->after('inner_number')->nullable();
            $table->string('weight')->after('color')->nullable();
            $table->string('extra')->after('weight')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_cards', function (Blueprint $table) {
            $table->dropColumn(['color', 'weight', 'extra']);
        });
    }
};
