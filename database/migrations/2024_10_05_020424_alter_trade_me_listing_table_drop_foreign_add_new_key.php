<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trade_me_listings', function (Blueprint $table) {
            $table->unsignedBigInteger('car_pdr_position_id')->after('id');
            Schema::dropIfExists('parts');
            $table->dropForeign('trade_me_listings_part_id_foreign');
            $table->dropColumn('part_id');
            $table->foreign('car_pdr_position_id')
                ->references('id')
                ->on('car_pdr_positions');
        });
    }

    public function down(): void
    {
        Schema::table('trade_me_listings', function (Blueprint $table) {
            //
        });
    }
};
