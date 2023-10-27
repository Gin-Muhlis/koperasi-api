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
        Schema::table('sales_details', function (Blueprint $table) {
            $table
                ->foreign('sale_id')
                ->references('id')
                ->on('sales')
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
        Schema::table('sales_details', function (Blueprint $table) {
            //
        });
    }
};
