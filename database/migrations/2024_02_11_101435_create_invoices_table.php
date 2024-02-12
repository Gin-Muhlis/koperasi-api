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
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('member_id');
			$table->unsignedBigInteger('user_id');
			$table->double('principal_saving');
			$table->double('mandatory_saving');
			$table->double('special_mandatory_saving');
			$table->double('voluntary_saving');
			$table->double('recretional_saving');
			$table->double('receivable');
			$table->double('account_receivable');
			$table->string('month_year', 20);
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
