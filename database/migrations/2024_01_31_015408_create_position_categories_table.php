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
        Schema::create('position_categories', function (Blueprint $table) {
            $table->id();
            $table->string('position', 100);
            $table->double('pokok');
            $table->double('wajib');
            $table->double('wajib_khusus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_categories');
    }
};
