<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_base_item_pdr_positions_markets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomenclature_base_item_pdr_positions_id');
            $table->unsignedInteger('markets_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_base_item_pdr_positions_markets');
    }
};
