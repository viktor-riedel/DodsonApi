<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_me_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('oauth_token')->nullable();
            $table->string('oauth_token_secret')->nullable();
            $table->string('oauth_verifier')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('environment')->nullable();
            $table->string('access_token')->nullable();
            $table->string('access_token_secret')->nullable();
            $table->integer('authorized_by')->nullable();
            $table->boolean('authorized')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_me_tokens');
    }
};
