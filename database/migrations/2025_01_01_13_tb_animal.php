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
        Schema::create('tb_animal', function (Blueprint $table) {
            $table->foreignId('announcement_id')->primary()->constrained('tb_announcement')->onDelete('cascade');
            $table->foreignId('breed_id')->constrained('tb_breed')->onDelete('cascade');
            $table->foreignId('specie_id')->constrained('tb_specie')->onDelete('cascade');
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->string('color');
            $table->enum('age', ['puppy','adult','senior']);
            $table->enum('size', ['small', 'medium', 'large']);
            $table->boolean('disability')->nullable();
            $table->boolean('vaccinated')->nullable();
            $table->boolean('dewormed')->nullable();
            $table->boolean('castrated')->nullable();
            $table->string('image_profile');
            $table->date('last_seen_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_animal');
    }
};
