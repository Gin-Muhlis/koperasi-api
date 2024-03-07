<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('invoices', function (Blueprint $table) {
			$table->id();
			$table->string('invoice_code', 10);
			$table->string('invoice_name', 100);
			$table->date('date');
			$table->date('due_date');
			$table->enum('payment_source', ['gaji pns', 'gaji p3k', 'komite', 'TPP']);
			$table->enum('status', ['belum bayar', 'dibayar']);
			$table->date('payment_date')->nullable();
			$table->unsignedBigInteger('user_id');
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('invoices');
	}
};
