<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_role')->insert([
            ['name' => 'admin', 'description' => 'Administrador do Sistema'],
            ['name' => 'user', 'description' => 'Usuário do Sistema']
        ]);
    }
}
