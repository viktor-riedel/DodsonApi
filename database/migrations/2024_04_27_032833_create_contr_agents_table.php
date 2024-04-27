<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contr_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('person_name')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contr_agents');
    }
};
