<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::table('invoices', function (Blueprint $table) {
			$table
				->foreign('member_id')
				->references('id')
				->on('members')
				->onDelete('CASCADE')
				->onUpdate('CASCADE');

			$table
				->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('CASCADE')
				->onUpdate('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::table('invoices', function (Blueprint $table) {
			//
		});
	}
};
