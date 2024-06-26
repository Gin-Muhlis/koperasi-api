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
		Schema::create('savings', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('member_id');
			$table->double('amount');
			$table->unsignedBigInteger('sub_category_id');
			$table->date('date');
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('invoice_id')->nullable();
			$table->text('description');
			$table->string('month_year', 20)->nullable();
			$table->enum('status', ['belum bayar', 'dibayar']);
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('savings');
	}
};
