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
        Schema::create('tb_announcement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
            $table->foreignId('form_id')->constrained('tb_form')->onDelete('cascade');
            $table->enum('type', ['lost', 'donation']);
            $table->string('description', 1000);
            $table->integer('times_favorited')->default(0);
            $table->string('main_image', 255);
            $table->string('address', 255);
            $table->string('contact_phone', 20);
            $table->string('contact_email', 255);
            $table->string('last_seen_latitude', 255);
            $table->string('last_seen_longitude', 255);
            $table->boolean('active')->default(true);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_announcement');
    }
};
