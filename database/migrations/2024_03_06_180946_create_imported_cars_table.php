<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('imported_cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->string('external_id')->nullable()->index();
            $table->string('imported_from');
            $table->date('date_import');
            $table->unsignedBigInteger('importedBy');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('car_id')
                ->references('id')
                ->on('cars')
                ->cascadeOnDelete();

            $table->foreign('importedBy')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imported_cars');
    }
};
