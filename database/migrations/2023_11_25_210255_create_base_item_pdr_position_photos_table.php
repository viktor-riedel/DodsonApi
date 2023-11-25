<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomenclature_base_item_pdr_position_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\NomenclatureBaseItemPdrPosition::class);
            $table->string('folder_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('mime')->nullable();
            $table->boolean('main_photo')->default(false);
            $table->boolean('is_video')->default(false);
            $table->string('video_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_item_pdr_position_photos');
    }
};
