<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_finances', function (Blueprint $table) {
            $table->boolean('parts_for_sale')->default(false)->after('car_is_for_sale');
        });
    }

    public function down(): void
    {
        Schema::table('car_finances', function (Blueprint $table) {
            $table->dropColumn('parts_for_sale');
        });
    }
};
