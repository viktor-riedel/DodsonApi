<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('mobile_phone')->nullable();
            $table->string('landline_phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('comment')->nullable();
            $table->boolean('wholesaler')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_cards');
    }
};
