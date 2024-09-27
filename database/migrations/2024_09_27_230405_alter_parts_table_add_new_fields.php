<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->string('oem_number')->nullable()->after('ic_number')->index();
            $table->string('hash_id')->nullable()->after('inner_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('oem_number');
            $table->dropColumn('hash_id');
        });
    }
};
