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
        Schema::create("tb_form", function (Blueprint $table) {
           $table->id();
           $table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
           $table->string("title", 255)->unique();
           $table->string("url", 255);
           $table->string('payload', 10000);
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tb_form");
    }
};
