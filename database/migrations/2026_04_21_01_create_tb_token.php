<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('tb_token', function (Blueprint $table) {
      $table->id();
      $table->string('type');
      $table->string('token')->unique();
      $table->longText('payload');
      $table->timestamp('expires_at', 0)->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('tb_token');
  }
};
