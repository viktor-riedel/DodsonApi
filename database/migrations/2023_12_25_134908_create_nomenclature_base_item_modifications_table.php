<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_base_item_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomenclature_base_item_pdr_position_id')
                ->constrained('nomenclature_base_item_pdr_positions', 'id', 'base_item_pdr_id')
                ->cascadeOnDelete();
            $table->string('header')->nullable();
            $table->string('generation')->nullable();
            $table->string('modification')->nullable();
            $table->string('engine_name')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('engine_size')->nullable();
            $table->string('engine_power')->nullable();
            $table->smallInteger('doors')->nullable();
            $table->string('transmission')->nullable();
            $table->string('drive_train')->nullable();
            $table->string('chassis')->nullable();
            $table->string('body_type')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('restyle')->nullable();
            $table->boolean('not_restyle')->nullable();
            $table->string('month_from')->nullable();
            $table->string('month_to')->nullable();
            $table->string('year_from')->nullable();
            $table->string('year_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_base_item_modifications');
    }
};
