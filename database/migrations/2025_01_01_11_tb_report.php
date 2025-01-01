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
        Schema::create('tb_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
            $table->foreignId('announcement_id')->constrained('tb_announcement')->onDelete('cascade');
            $table->string('description', 255);
            $table->enum('type', ['inappropriate content', 'fake information', 'duplicity', 'other']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_report');
    }
};
