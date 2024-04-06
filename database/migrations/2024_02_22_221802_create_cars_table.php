<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('cars');

        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('parent_inner_id')->index();
            $table->string('make')->index();
            $table->string('model')->index();
            $table->string('generation')->index();
            $table->string('chassis')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('color')->nullable();
            $table->integer('engine')->nullable();
            $table->integer('car_status')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
