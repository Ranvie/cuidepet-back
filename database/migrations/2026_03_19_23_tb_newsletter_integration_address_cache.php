<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	
  /**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_newsletter_integration_address_cache', function (Blueprint $table) {
			$table->id();
			$table->foreignId('newsletter_id')->constrained('tb_newsletter')->onDelete('cascade');
			$table->foreignId('address_cache_id')->constrained('tb_integration_address_cache')->onDelete('cascade');
			$table->string('hash', 64)->unique('NEWSLETTER_INTEGRATION_ADDRESS_CACHE_UK_01');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_newsletter_integration_address_cache');
	}
};
