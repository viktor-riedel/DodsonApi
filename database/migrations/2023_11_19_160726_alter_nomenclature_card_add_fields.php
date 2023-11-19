<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_cards', function (Blueprint $table) {
            $table->renameColumn('name', 'name_eng');
            $table->string('name_ru')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_cards', function (Blueprint $table) {
            $table->renameColumn('name_eng', 'name');
            $table->dropColumn('name_ru');
        });
    }
};
