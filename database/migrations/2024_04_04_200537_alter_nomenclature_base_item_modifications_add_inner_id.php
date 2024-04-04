<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_modifications', function (Blueprint $table) {
            $table->string('inner_id')->nullable()->index()
                ->after('nomenclature_base_item_pdr_position_id');
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_modifications', function (Blueprint $table) {
            $table->dropColumn('inner_id');
        });
    }
};
