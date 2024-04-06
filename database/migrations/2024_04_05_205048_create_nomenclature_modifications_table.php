<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_modifications', function (Blueprint $table) {
            $table->id();
            $table->integer('modificationable_id');
            $table->string('modificationable_type');
            $table->string('inner_id')->nullable()->index();
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
            $table->string('years_string')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_modifications');
    }
};
