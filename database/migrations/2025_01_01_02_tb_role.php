<?php

use Database\Seeders\RoleSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_role', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->string('description', 200);
        });

        Artisan::call('db:seed', ['--class' => 'RoleSeeder']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_role');
    }
};
