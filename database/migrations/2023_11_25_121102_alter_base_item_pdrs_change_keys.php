<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('nomenclature_base_item_pdr_cards');
        Schema::create('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomenclature_base_item_pdr_position_id')
                ->constrained('nomenclature_base_item_pdr_positions', 'id', 'nomenclatures_base_item_position_idx')
                ->cascadeOnDelete();
            $table->string('name_eng')->nullable();
            $table->string('name_ru')->nullable();
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
            $table->string('color')->nullable();
            $table->string('weight')->nullable();
            $table->string('extra')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_base_item_pdr_cards');
    }
};
