<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('tb_animal', function (Blueprint $table) {
			$table->id();
			$table->foreignId('announcement_id')->constrained('tb_announcement')->onDelete('cascade');
			$table->foreignId('breed_id')->constrained('tb_breed')->onDelete('cascade');
			$table->string('name', 255);
			$table->enum('gender', ['male', 'female']);
			$table->string('color', 50);
			$table->enum('age', ['puppy','adult','senior']);
			$table->enum('size', ['small', 'medium', 'large']);
			$table->boolean('disability')->nullable()->default(false);
			$table->boolean('vaccinated')->nullable()->default(false);
			$table->boolean('dewormed')->nullable()->default(false);
			$table->boolean('castrated')->nullable()->default(false);
			$table->string('image_profile');
			$table->date('last_seen_date')->useCurrent();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('tb_animal');
	}
};
