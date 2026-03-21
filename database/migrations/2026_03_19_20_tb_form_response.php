<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	
  /**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_form_response', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
			$table->foreignId('announcement_id')->constrained('tb_announcement')->onDelete('cascade');
			$table->text('payload');
      $table->timestamp('created_at', 0)->useCurrent();
			$table->unique(['user_id', 'announcement_id'], 'FORM_RESPONSE_UK_01');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_form_response');
	}
};
