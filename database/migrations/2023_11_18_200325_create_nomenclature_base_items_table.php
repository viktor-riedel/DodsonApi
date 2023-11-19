<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_base_items', function (Blueprint $table) {
            $table->id();
            $table->string('make')->index();
            $table->string('model')->index();
            $table->string('header')->index();
            $table->string('generation')->nullable();
            $table->string('year_start')->nullable();
            $table->string('year_stop')->nullable();
            $table->string('month_start')->nullable();
            $table->string('month_stop')->nullable();
            $table->string('preview_image')->nullable();
            $table->boolean('restyle')->default(false);
            $table->boolean('not_restyle')->default(false);
            $table->integer('doors')->nullable();
            $table->string('body_type')->nullable();
            $table->string('engine_name')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('engine_size')->nullable();
            $table->integer('engine_power')->nullable();
            $table->string('transmission_type')->nullable();
            $table->string('drive_train')->nullable();
            $table->string('chassis')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_base_items');
    }
};
