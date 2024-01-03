<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('base_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomenclature_base_item_id')
                ->constrained('nomenclature_base_items', 'id',
                'nom_base_id');
            $table->string('header')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('generation')->nullable();
            $table->string('generation_number')->nullable();
            $table->string('body_type')->nullable();
            $table->smallInteger('doors')->nullable();
            $table->integer('month_start')->nullable();
            $table->integer('month_stop')->nullable();
            $table->integer('year_start')->nullable();
            $table->integer('year_stop')->nullable();
            $table->boolean('restyle')->nullable();
            $table->boolean('not_restyle')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_cars');
    }
};
