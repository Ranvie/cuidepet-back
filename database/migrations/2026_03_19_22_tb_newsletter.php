<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	
  /**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_newsletter', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->nullable()->constrained('tb_user')->onDelete('cascade');
			$table->string('email')->unique('NEWSLETTER_UK_01');
			$table->boolean('email_confirmed')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_newsletter');
	}
};
