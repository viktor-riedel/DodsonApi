<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_number')->index();
            $table->smallInteger('order_status')->default(0);
            $table->integer('order_total')->nullable();
            $table->string('country_code')->nullable();
            $table->string('invoice_url')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
