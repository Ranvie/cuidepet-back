<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('tb_report', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
      $table->foreignId('report_message_id')->constrained('tb_report_message')->onDelete('cascade');
      $table->foreignId('announcement_id')->nullable()->constrained('tb_announcement')->onDelete('cascade');
      $table->foreignId('form_id')->nullable()->constrained('tb_form')->onDelete('cascade');
      $table->text('description');
      $table->timestamp('created_at')->useCurrent();
      $table->unique(['user_id', 'announcement_id', 'form_id'], 'REPORT_UK_01');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('tb_report');
  }
};
