<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('order_number');
            $table->string('status_en')->nullable()->after('order_number');
            $table->string('status_ru')->nullable()->after('order_number');
            $table->integer('total_amount')->nullable()->after('order_number');
            $table->integer('mvr_price')->nullable()->after('order_number');
            $table->integer('extra_price')->nullable()->after('order_number');
            $table->integer('package_price')->nullable()->after('order_number');
            $table->integer('mvr_commission')->nullable()->after('order_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('reference');
            $table->dropColumn('status_en');
            $table->dropColumn('status_ru');
            $table->dropColumn('total_amount');
            $table->dropColumn('mvr_price');
            $table->dropColumn('extra_price');
            $table->dropColumn('package_price');
            $table->dropColumn('mvr_commission');
        });
    }
};
