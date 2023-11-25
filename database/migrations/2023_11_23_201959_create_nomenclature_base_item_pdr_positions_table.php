<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_base_item_pdr_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomenclature_base_item_pdr_id')
                ->constrained('nomenclature_base_item_pdrs', 'id', 'nomenclatures_base_item_pdrs_positions')
                ->cascadeOnDelete();
            $table->string('ic_number')->nullable();
            $table->string('oem_number')->nullable();
            $table->mediumText('ic_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_base_item_pdr_positions');
    }
};
