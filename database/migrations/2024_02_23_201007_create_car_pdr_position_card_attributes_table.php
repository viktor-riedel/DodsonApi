<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_pdr_position_card_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_pdr_position_card_id');
            $table->string('color')->nullable();
            $table->string('weight')->nullable();
            $table->string('volume')->nullable();
            $table->boolean('trademe')->default(false);
            $table->boolean('drom')->default(false);
            $table->boolean('avito')->default(false);
            $table->boolean('dodson')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('car_pdr_position_card_id', 'idx_car_pdr')
                ->references('id')
                ->on('car_pdr_card_position_cards')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_pdr_position_card_attributes');
    }
};
