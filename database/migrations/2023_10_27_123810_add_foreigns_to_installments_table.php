<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::table('installments', function (Blueprint $table) {
			$table
				->foreign('loan_id')
				->references('id')
				->on('loans')
				->onUpdate('CASCADE')
				->onDelete('CASCADE');

			$table
				->foreign('sub_category_id')
				->references('id')
				->on('sub_categories')
				->onUpdate('CASCADE')
				->onDelete('CASCADE');

			$table
				->foreign('user_id')
				->references('id')
				->on('users')
				->onUpdate('CASCADE')
				->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::table('installments', function (Blueprint $table) {
			//
		});
	}
};
