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
            $table->integer('model_year')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('engine_size')->nullable();
            $table->integer('power')->nullable();
            $table->string('fuel')->nullable();
            $table->string('transmission')->nullable();
            $table->string('drivetrain')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_cars');
    }
};
