<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('import_items', function (Blueprint $table) {
            $table->id();
            $table->integer('importable_id');
            $table->string('importable_type');
            $table->integer('imported_id');
            $table->string('imported_from')->nullable();
            $table->integer('imported_by')->default(0);
            $table->string('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_items');
    }
};
