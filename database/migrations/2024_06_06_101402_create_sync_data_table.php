<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sync_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('syncable_id');
            $table->string('syncable_type');
            $table->string('document_number')->nullable();
            $table->string('document_date')->nullable();
            $table->string('document_url')->nullable();
            $table->string('document_type')->nullable();
            $table->string('data')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('deleted_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_data');
    }
};
