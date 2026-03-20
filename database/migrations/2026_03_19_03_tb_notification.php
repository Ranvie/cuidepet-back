<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_notification', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
			$table->foreignId('notification_template_id')->constrained('tb_notification_template')->onDelete('cascade');
			$table->boolean('viewed')->default(false);
			$table->timestamp('created_at', 0)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_notification');
	}
};
