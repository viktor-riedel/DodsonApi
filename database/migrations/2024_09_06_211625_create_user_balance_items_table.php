<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_balance_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_balance_id');
            $table->string('document_name')->nullable();
            $table->integer('closing_balance')->nullable()->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_balance_id')
                ->references('id')
                ->on('user_balances')
                ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_balance_items');
    }
};
