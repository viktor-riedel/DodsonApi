<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdrs', function (Blueprint $table) {
            $table->integer('parts_list_id')->nullable()->after('is_folder');
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdrs', function (Blueprint $table) {
            $table->dropColumn('parts_list_id');
        });
    }
};
