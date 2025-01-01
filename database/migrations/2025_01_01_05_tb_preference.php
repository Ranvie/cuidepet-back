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
        Schema::create('tb_preference', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('tb_user')->onDelete('cascade');
            $table->boolean('receive_region_alarms')->default(false);
            $table->boolean('receive_alarms_on_email')->default(false);
            $table->boolean('receive_news')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_preference');
    }
};
