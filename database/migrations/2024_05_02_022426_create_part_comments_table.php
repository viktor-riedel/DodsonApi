<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('part_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('commentable_id')->index();
            $table->string('commentable_type');
            $table->string('comment')->nullable();
            $table->integer('user_id')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_comments');
    }
};
