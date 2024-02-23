<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('mediable_id')->index();
            $table->string('mediable_type');
            $table->string('url')->nullable();
            $table->string('mime')->nullable();
            $table->string('original_file_name')->nullable();
            $table->string('folder_name')->nullable();
            $table->string('extension')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('special_flag')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
