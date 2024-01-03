<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->dropColumn('default_price');
            $table->dropColumn('default_retail_price');
            $table->dropColumn('default_wholesale_price');
            $table->dropColumn('default_special_price');
            $table->dropColumn('wholesale_rus_price');
            $table->dropColumn('wholesale_nz_price');
            $table->dropColumn('retail_rus_price');
            $table->dropColumn('retail_nz_price');
            $table->dropColumn('special_rus_price');
            $table->dropColumn('special_nz_price');
            $table->dropColumn('status');
            $table->dropColumn('condition');
            $table->dropColumn('tag');
            $table->dropColumn('yard');
            $table->dropColumn('bin');
            $table->dropColumn('is_new');
            $table->dropColumn('is_scrap');
            $table->dropColumn('inner_number');
            $table->dropColumn('mileage');
            $table->dropColumn('extra');

            $table->unsignedDouble('price_nz_wholesale')->nullable();
            $table->unsignedDouble('price_nz_retail')->nullable();
            $table->unsignedDouble('price_ru_wholesale')->nullable();
            $table->unsignedDouble('price_ru_retail')->nullable();
            $table->unsignedDouble('price_jp_minimum_buy')->nullable();
            $table->unsignedDouble('price_jp_maximum_buy')->nullable();
            $table->boolean('trademe')->default(false);
            $table->boolean('drom')->default(false);
            $table->boolean('avito')->default(false);
            $table->boolean('dodson')->default(false);
            $table->boolean('nova')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('nomenclature_base_item_pdr_cards', function (Blueprint $table) {
            $table->integer('default_price')->nullable();
            $table->integer('default_retail_price')->nullable();
            $table->integer('default_wholesale_price')->nullable();
            $table->integer('default_special_price')->nullable();
            $table->integer('wholesale_rus_price')->nullable();
            $table->integer('wholesale_nz_price')->nullable();
            $table->integer('retail_rus_price')->nullable();
            $table->integer('retail_nz_price')->nullable();
            $table->integer('special_rus_price')->nullable();
            $table->integer('special_nz_price')->nullable();
            $table->integer('status')->nullable();
            $table->string('condition')->nullable();
            $table->string('tag')->nullable();
            $table->string('yard')->nullable();
            $table->string('bin')->nullable();
            $table->boolean('is_new')->nullable();
            $table->boolean('is_scrap')->nullable();
            $table->string('inner_number')->nullable();
            $table->string('color')->nullable();
        });
    }
};
