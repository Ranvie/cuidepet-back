<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('tb_form', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
      $table->string('title', 255);
      $table->text('payload');
      $table->boolean('active')->default(true);
      $table->boolean('blocked')->default(false);
      $table->timestamp('created_at', 0)->useCurrent();
      $table->timestamp('updated_at', 0)->useCurrent()->useCurrentOnUpdate();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('tb_form');
  }
};
