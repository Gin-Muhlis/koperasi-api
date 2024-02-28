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
        Schema::table('savings', function (Blueprint $table) {
            $table->index(['id', 'member_id', 'sub_category_id', 'invoice_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings', function (Blueprint $table) {
            //
        });
    }
};
