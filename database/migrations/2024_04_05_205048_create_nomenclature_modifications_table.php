<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_modifications', function (Blueprint $table) {
            $table->id();
            $table->integer('modificationable_id');
            $table->string('modificationable_type');
            $table->string('inner_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclature_modifications');
    }
};
