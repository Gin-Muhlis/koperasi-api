<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('payments', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('invoice_id');
			$table->double('amount');
			$table->date('date_payment');
			$table->string('no_rek')->nullable();
			$table->string('payer')->nullable();
			$table->enum('payment_method', ['cash', 'transfer']);
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('payments');
	}
};
