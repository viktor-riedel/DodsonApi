<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_base_item_pdrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomenclature_base_item_id')
                ->constrained('nomenclature_base_items', 'id', 'nomenclatures_base_item_id')
                ->cascadeOnDelete();
            $table->integer('parent_id')->default(false);
            $table->string('item_name_eng')->nullable();
            $table->string('item_name_ru')->nullable();
            $table->boolean('is_folder')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_base_item_pdrs');
    }
};
