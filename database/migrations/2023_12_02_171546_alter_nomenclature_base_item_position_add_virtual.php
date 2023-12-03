<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdr_positions', function (Blueprint $table) {
            $table->boolean('is_virtual')->default(false)
                ->after('ic_description');
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdr_positions', function (Blueprint $table) {
            $table->dropColumn('is_virtual');
        });
    }
};
