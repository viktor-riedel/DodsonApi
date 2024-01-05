<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdr_positions', function (Blueprint $table) {
            $table->string('item_name_eng')->after('nomenclature_base_item_pdr_id')->nullable();
            $table->string('item_name_ru')->after('nomenclature_base_item_pdr_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdr_positions', function (Blueprint $table) {
            $table->dropColumn('item_name_eng');
            $table->dropColumn('item_name_ru');
        });
    }
};
