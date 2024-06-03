<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->boolean('with_engine')->default(false)->after('car_id');
            $table->boolean('without_engine')->default(false)->after('car_id');
            $table->text('comment')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('with_engine');
            $table->dropColumn('without_engine');
            $table->dropColumn('comment');
        });
    }
};
