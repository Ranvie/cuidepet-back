<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('tb_announcement', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
      $table->foreignId('form_id')->nullable()->constrained('tb_form');
      $table->foreignId('address_id')->constrained('tb_address');
      $table->enum('type', ['lost', 'donation']);
      $table->text('description');
      $table->string('main_image', 255)->nullable();
      $table->string('contact_phone', 20)->nullable();
      $table->string('contact_email', 255)->nullable();
      $table->boolean('active')->default(true);
      $table->boolean('blocked')->default(false);
      $table->boolean('status')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('tb_announcement');
  }
};
