<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder {

  /**
   * Roda os seeders para popular a tabela de usuários.
   * @return void
   */
  public function run(): void {
    DB::table('tb_user')->insert([
      ['id' => 1, 'username' => 'admin',  'email' => 'cuidepet.admin@email.com',  'password' => bcrypt('123456'), 'email_verified_at' => now(), 'active' => 1],
      ['id' => 2, 'username' => 'rangel', 'email' => 'cuidepet.rangel@email.com', 'password' => bcrypt('123456'), 'email_verified_at' => now(), 'active' => 1],
      ['id' => 3, 'username' => 'eric',   'email' => 'cuidepet.eric@email.com',   'password' => bcrypt('123456'), 'email_verified_at' => now(), 'active' => 1]
    ]);
  }
}
