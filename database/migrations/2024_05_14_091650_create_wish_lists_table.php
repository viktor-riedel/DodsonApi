<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wish_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wishable_id')->index();
            $table->string('wishable_type');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wish_lists');
    }
};
