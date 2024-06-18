<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('selling_map_items', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0);
            $table->string('item_name_eng');
            $table->string('item_name_ru')->nullable();
            $table->text('comment')->nullable();
            $table->integer('price_jpy')->nullable();
            $table->integer('price_rub')->nullable();
            $table->integer('price_nzd')->nullable();
            $table->integer('price_mng')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selling_map_items');
    }
};
