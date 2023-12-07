<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('part_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0);
            $table->string('item_name_eng')->nullable();
            $table->string('item_name_ru')->nullable();
            $table->boolean('is_folder')->default(false);
            $table->boolean('is_virtual')->default(false);
            $table->string('icon_name')->nullable();
            $table->string('key')->nullable();
            $table->boolean('is_used')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_lists');
    }
};
