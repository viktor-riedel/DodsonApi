<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('car_mvr')->nullable()->after('car_status');
            $table->text('comment')->nullable()->after('car_status');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['car_mvr', 'comment', 'generation']);
        });
    }
};
