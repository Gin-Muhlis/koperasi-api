<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('members', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('name', 100);
			$table->string('email')->unique();
			$table->text('address');
			$table->string('phone_number');
			$table->enum('gender', ['L', 'P']);
			$table->string('religion', 20);
			$table->enum('position', ['pns', 'p3k', 'cpns']);
			$table->unsignedBigInteger('group_id');
			$table->string('image')->nullable();
			$table->date('date_activation');
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('members');
	}
};
