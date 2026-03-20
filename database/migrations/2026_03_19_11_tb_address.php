<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('tb_address', function (Blueprint $table) {
      $table->id();
      $table->foreignId('integration_address_cache_id')->constrained('tb_integration_address_cache')->onDelete('cascade');
      $table->string('street', 255);
      $table->string('neighborhood', 255);
      $table->string('number', 10)->nullable();
      $table->string('complement', 255)->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('tb_address');
  }
};
