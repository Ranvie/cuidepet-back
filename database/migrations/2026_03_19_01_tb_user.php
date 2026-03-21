<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_user', function (Blueprint $table) {
			$table->id();
			$table->string('username', 255);
			$table->string('email', 255)->unique('ROLE_UK_01');
			$table->string('password', 255);
			$table->string('image_profile', 255)->nullable();
			$table->string('phone', 20)->nullable();
			$table->timestamp('email_verified_at')->nullable();
			$table->boolean('active')->default(false);
      $table->timestamp('created_at', 0)->useCurrent();
      $table->timestamp('updated_at', 0)->useCurrent()->useCurrentOnUpdate();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_user');
	}
};
