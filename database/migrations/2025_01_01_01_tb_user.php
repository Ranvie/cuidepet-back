<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_user', function (Blueprint $table) {
            $table->id();
            $table->string('username', 255);
            $table->string('email', 255)->unique();
            $table->string('secondary_email', 255)->unique()->nullable();
            $table->string('password', 255);
            $table->string('image_profile')->nullable();
            $table->string('main_phone', 20)->nullable();
            $table->string('secondary_phone', 20)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_user');
    }
};
