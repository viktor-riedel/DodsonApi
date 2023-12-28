<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('related_base_item_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomenclature_base_item_pdr_position_id')
                ->constrained('nomenclature_base_item_pdr_positions', 'id', 'reused_position_idx')
                ->cascadeOnDelete();
            $table->foreignId('related_id')
                ->constrained('nomenclature_base_item_pdr_positions', 'id', 'reused_related_position_idx')
                ->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('related_base_item_positions');
    }
};
