<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('inner_id');
            $table->string('stock_number')->nullable()->index();
            $table->string('ic_number')->nullable()->index();
            $table->string('ic_description')->nullable()->index();
            $table->string('make')->index();
            $table->string('model')->index();
            $table->string('year')->index();
            $table->string('mileage');
            $table->string('amount')->default(1);
            $table->string('item_name_eng')->nullable();
            $table->string('item_name_ru')->nullable();
            $table->string('item_name_jp')->nullable();
            $table->string('item_name_mng')->nullable();
            $table->string('original_barcode')->nullable();
            $table->string('generated_barcode')->nullable();
            $table->integer('price_jpy')->nullable();
            $table->integer('price_nzd')->nullable();
            $table->integer('price_mng')->nullable();
            $table->string('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
