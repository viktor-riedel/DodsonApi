<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('car_id')->nullable()->index();
            $table->unsignedBigInteger('part_id')->nullable()->index();
            $table->boolean('with_engine')->default(false);
            $table->boolean('without_engine')->default(false);
            $table->integer('price_with_engine')->nullable();
            $table->integer('price_without_engine')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
