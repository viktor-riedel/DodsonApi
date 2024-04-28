<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('status_update_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('car_id');
            $table->integer('old_status');
            $table->integer('new_status');
            $table->integer('user_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_update_logs');
    }
};
