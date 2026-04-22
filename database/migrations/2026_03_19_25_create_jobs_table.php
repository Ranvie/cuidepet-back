<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criar as tabelas de jobs e failed_jobs, utilizadas pelo sistema de filas do Laravel.
 * A tabela de jobs armazena as tarefas pendentes, enquanto a tabela de failed_jobs registra as tarefas que falharam durante a execução.
 */
return new class extends Migration {

  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('jobs', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('queue')->index();
      $table->longText('payload');
      $table->unsignedTinyInteger('attempts');
      $table->unsignedInteger('reserved_at')->nullable();
      $table->unsignedInteger('available_at');
      $table->unsignedInteger('created_at');
    });

    Schema::create('failed_jobs', function (Blueprint $table) {
      $table->id();
      $table->string('uuid')->unique();
      $table->text('connection');
      $table->text('queue');
      $table->longText('payload');
      $table->longText('exception');
      $table->timestamp('failed_at')->useCurrent();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('jobs');
    Schema::dropIfExists('failed_jobs');
  }
};
