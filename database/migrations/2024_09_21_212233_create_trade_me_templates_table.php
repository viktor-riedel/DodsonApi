<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_me_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('delivery_options')->nullable();
            $table->string('default_duration')->nullable();
            $table->string('payments_options')->nullable();
            $table->boolean('update_prices')->default(true);
            $table->boolean('relist')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_me_templates');
    }
};
