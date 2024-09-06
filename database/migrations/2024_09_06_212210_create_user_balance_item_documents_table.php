<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_balance_item_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_balance_item_id');
            $table->string('document_description')->nullable();
            $table->integer('amount')->nullable()->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_balance_item_id')
                ->references('id')
                ->on('user_balance_items');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_balance_item_documents');
    }
};
