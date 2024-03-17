<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profile_apps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('chairmans_name');
            $table->string('secretary_name');
            $table->string('treasurer_name');
            $table->text('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_apps');
    }
};
