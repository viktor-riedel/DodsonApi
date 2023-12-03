<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdrs', function (Blueprint $table) {
            $table->unsignedInteger('nomenclature_base_item_pdr_position_id')
                ->nullable()
                ->after('nomenclature_base_item_pdr_card_id');
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdrs', function (Blueprint $table) {
            $table->dropColumn('nomenclature_base_item_pdr_position_id');
        });
    }
};
