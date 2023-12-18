<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_items', function (Blueprint $table) {
            $table->dropColumn(['header', 'year_start', 'year_stop',
                'month_start', 'month_stop', 'restyle', 'not_restyle', 'doors',
                'body_type', 'engine_name', 'engine_type', 'engine_size',
                'engine_power', 'transmission_type', 'drive_train', 'chassis']);
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_items', function (Blueprint $table) {
            $table->string('header')->nullable();
            $table->string('year_start')->nullable();
            $table->string('year_stop')->nullable();
            $table->string('month_start')->nullable();
            $table->string('month_stop')->nullable();
            $table->string('restyle')->nullable();
            $table->string('not_restyle')->nullable();
            $table->string('doors')->nullable();
            $table->string('body_type')->nullable();
            $table->string('engine_name')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('engine_size')->nullable();
            $table->string('engine_power')->nullable();
            $table->string('transmission_type')->nullable();
            $table->string('drive_train')->nullable();
            $table->string('chassis')->nullable();
        });
    }
};
