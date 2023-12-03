<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->string('volume')->nullable();
            $table->integer('mileage')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->dropColumn(['volume', 'mileage']);
        });
    }
};
