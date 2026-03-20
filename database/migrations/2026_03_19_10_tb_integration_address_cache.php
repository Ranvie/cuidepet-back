<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('tb_integration_address_cache', function (Blueprint $table) {
      $table->id();
      $table->string('latitude', 255);
      $table->string('longitude', 255);
      $table->string('zipcode', 9)->unique('INTEGRATION_ADDRESS_CACHE_UK_01');
      $table->string('state', 50);
      $table->string('city', 60);
      $table->string('neighborhood', 255)->nullable();
      $table->string('street', 255)->nullable();
      $table->string('source', 255);
      $table->timestamp('expires_at')->useCurrent()->useCurrentOnUpdate();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    });

    DB::statement('ALTER TABLE tb_integration_address_cache ADD COLUMN `point` POINT NOT NULL AFTER `id`');
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('tb_integration_address_cache');
  }
};
