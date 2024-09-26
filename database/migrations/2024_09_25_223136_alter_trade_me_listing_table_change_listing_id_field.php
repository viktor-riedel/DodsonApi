<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trade_me_listings', function (Blueprint $table) {
            $table->unsignedBigInteger('listing_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('trade_me_listings', function (Blueprint $table) {
            $table->integer('listing_id')->change();
        });
    }
};
