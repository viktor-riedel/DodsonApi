<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_pdr_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_pdr_id');
            $table->string('item_name_ru')->nullable();
            $table->string('item_name_eng')->nullable();
            $table->string('ic_number')->nullable();
            $table->string('oem_number')->nullable();
            $table->text('ic_description')->nullable();
            $table->boolean('is_virtual')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('car_pdr_id')
                ->references('id')
                ->on('car_pdrs')
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
        Schema::dropIfExists('car_pdr_positions');
    }
};
