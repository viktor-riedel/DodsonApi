<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->double('default_price')->nullable();
            $table->double('default_wholesale_price')->nullable();
            $table->double('default_retail_price')->nullable();
            $table->double('default_special_price')->nullable();
            $table->double('wholesale_rus_price')->nullable();
            $table->double('wholesale_nz_price')->nullable();
            $table->double('retail_rus_price')->nullable();
            $table->double('retail_nz_price')->nullable();
            $table->double('special_rus_price')->nullable();
            $table->double('special_nz_price')->nullable();
            $table->mediumText('comment')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('status')->nullable();
            $table->string('condition')->nullable();
            $table->string('tag')->nullable();
            $table->string('yard')->nullable();
            $table->string('bin')->nullable();
            $table->boolean('is_new')->default(false);
            $table->boolean('is_scrap')->default(false);
            $table->string('ic_number')->nullable();
            $table->string('oem_number')->nullable();
            $table->string('inner_number')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_cards');
    }
};
