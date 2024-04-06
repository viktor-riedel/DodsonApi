<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('car_pdr_position_card_attributes', function (Blueprint $table) {
            $table->integer('amount')->after('dodson')->nullable();
            $table->unsignedInteger('ordered_for_user_id')->after('dodson')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('car_pdr_position_card_attributes', function (Blueprint $table) {
            $table->dropColumn('amount', 'ordered_for_user_id');
        });
    }
};
