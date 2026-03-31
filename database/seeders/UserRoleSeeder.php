<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder {

  /**
   * Roda os seeders para popular a tabela de perfis de usuários.
   * @return void
   */
  public function run(): void {
    DB::table('tb_user_role')->insert([
      ['id' => 1, 'user_id' => 1, 'role_id' => 1],
      ['id' => 2, 'user_id' => 1, 'role_id' => 2],
      ['id' => 3, 'user_id' => 2, 'role_id' => 1],
      ['id' => 4, 'user_id' => 2, 'role_id' => 2],
      ['id' => 5, 'user_id' => 3, 'role_id' => 1],
      ['id' => 6, 'user_id' => 3, 'role_id' => 2]
    ]);
  }
}
