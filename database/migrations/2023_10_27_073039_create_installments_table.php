<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('installments', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('code', 10);
			$table->unsignedBigInteger('loan_id');
			$table->unsignedBigInteger('sub_category_id');
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('invoice_id');
			$table->double('amount');
			$table->date('date');
			$table->enum('status', ['belum bayar', 'dibayar']);
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('installments');
	}
};
