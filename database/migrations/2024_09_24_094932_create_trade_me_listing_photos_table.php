<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_me_listing_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_me_listing_id');
            $table->string('image_url');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trade_me_listing_id')
                ->references('id')
                ->on('trade_me_listings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_me_listing_photos');
    }
};
