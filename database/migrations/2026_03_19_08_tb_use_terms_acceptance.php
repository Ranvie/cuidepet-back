<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_use_terms_acceptance', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
			$table->foreignId('use_terms_id')->constrained('tb_use_terms')->onDelete('cascade');
			$table->timestamp('accepted_at')->useCurrent();
			$table->unique(['user_id', 'use_terms_id'], 'USE_TERMS_ACCEPTANCE_UK_01');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_use_terms_acceptance');
	}
};
