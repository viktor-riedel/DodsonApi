<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_pdrs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->integer('car_pdr_card_id')->nullable();
            $table->integer('car_pdr_position_id')->nullable();
            $table->integer('parent_id')->default(0);
            $table->string('item_name_eng')->nullable();
            $table->string('item_name_ru')->nullable();
            $table->boolean('is_folder')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('car_id')
                ->references('id')
                ->on('cars')
                ->cascadeOnDelete();
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
        Schema::dropIfExists('car_pdrs');
    }
};
