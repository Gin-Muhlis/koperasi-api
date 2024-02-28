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
			$table->string('image');
			$table->string('no_rek')->nullable();
			$table->string('transfer_name')->nullable();
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
