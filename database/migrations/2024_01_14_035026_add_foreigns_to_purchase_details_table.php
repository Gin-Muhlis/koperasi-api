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
        Schema::table('purchase_details', function (Blueprint $table) {
            $table
            ->foreign('purchase_id')
            ->references('id')
            ->on('purchases')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');

        $table
            ->foreign('stuff_id')
            ->references('id')
            ->on('stuffs')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            //
        });
    }
};
