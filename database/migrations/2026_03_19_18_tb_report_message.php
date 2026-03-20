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
		Schema::create('tb_report_message', function (Blueprint $table) {
			$table->id();
			$table->string('motive', 255);
			$table->enum('type', ['announcement', 'form']);
		});

		Artisan::call('db:seed', ['--class' => 'ReportMessageSeeder']);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('tb_report_message');
	}
};
