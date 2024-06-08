<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')
                ->constrained('cars');
            $table->string('country_code')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_markets');
    }
};
