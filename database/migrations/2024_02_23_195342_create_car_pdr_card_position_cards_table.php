<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_pdr_card_position_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_pdr_position_card_id');
            $table->string('parent_inner_id')->nullable()->index();
            $table->string('name_eng')->nullable();
            $table->string('name_ru')->nullable();
            $table->text('comment')->nullable();
            $table->text('description')->nullable();
            $table->string('ic_number')->nullable()->index();
            $table->string('oem_number')->nullable()->index();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('car_pdr_position_card_id')
                ->references('id')
                ->on('car_pdr_positions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_pdr_card_position_cards');
    }
};
