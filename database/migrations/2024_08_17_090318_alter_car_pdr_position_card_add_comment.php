<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_pdr_position_cards', function (Blueprint $table) {
            $table->text('part_comment')->nullable()->after('comment');
        });
    }

    public function down(): void
    {
        Schema::table('car_pdr_position_cards', function (Blueprint $table) {
            $table->dropColumn('part_comment');
        });
    }
};
