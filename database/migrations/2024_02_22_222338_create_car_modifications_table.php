<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_modifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->string('body_type')->nullable();
            $table->string('chassis')->nullable();
            $table->string('generation')->nullable();
            $table->string('drive_train')->nullable();
            $table->string('header')->nullable();
            $table->string('month_from')->nullable();
            $table->string('month_to')->nullable();
            $table->boolean('restyle')->nullable();
            $table->string('transmission')->nullable();
            $table->string('year_from')->nullable();
            $table->string('years_string')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('car_id')
                ->references('id')
                ->on('cars')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_modifications');
    }
};
