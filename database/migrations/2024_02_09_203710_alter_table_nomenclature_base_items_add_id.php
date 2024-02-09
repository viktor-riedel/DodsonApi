<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_items', function (Blueprint $table) {
            $table->string('inner_id')->after('id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_items', function (Blueprint $table) {
            $table->dropColumn('inner_id');
        });
    }
};
