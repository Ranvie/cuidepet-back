<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_notification_template', function (Blueprint $table) {
			$table->id();
			$table->string('type', 255);
			$table->string('title', 100);
			$table->string('message', 255);
		});

		Artisan::call('db:seed', ['--class' => 'NotificationTemplateSeeder']);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_notification_template');
	}
};
