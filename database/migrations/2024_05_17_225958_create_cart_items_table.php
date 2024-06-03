<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('car_id')->nullable()->index();
            $table->string('part_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('cart_id')
                ->references('id')
                ->on('carts');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
