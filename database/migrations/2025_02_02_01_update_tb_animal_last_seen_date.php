<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_animal', function (Blueprint $table) {
            $table->date('last_seen_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }
};
