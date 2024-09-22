<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_me_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_id');
            $table->integer('listing_id');
            $table->unsignedBigInteger('listed_by');
            $table->string('title');
            $table->string('category');
            $table->string('short_description')->nullable();
            $table->text('description');
            $table->string('delivery_options')->nullable();
            $table->string('default_duration')->nullable();
            $table->string('payments_options')->nullable();
            $table->boolean('update_prices')->default(false);
            $table->boolean('relist')->default(false);
            $table->dateTime('relist_date')->nullable();
            $table->dateTime('update_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('listed_by')
                ->references('id')
                ->on('users');

            $table->foreign('part_id')
                ->references('id')
                ->on('parts');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_me_listings');
    }
};
