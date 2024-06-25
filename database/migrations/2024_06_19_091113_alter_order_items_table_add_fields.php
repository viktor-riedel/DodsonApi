<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('part_id');
            $table->string('item_status_en')->nullable()->after('comment');
            $table->string('item_status_ru')->nullable()->after('comment');
            $table->string('currency')->nullable()->after('price_jpy');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('item_status_en');
            $table->dropColumn('item_status_ru');
            $table->dropColumn('currency');
        });
    }
};
