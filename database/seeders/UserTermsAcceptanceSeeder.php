<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTermsAcceptanceSeeder extends Seeder {

  /**
   * Roda os seeders para popular a tabela de aceite de termos de uso.
   * @return void
   */
  public function run(): void {
    DB::table('tb_use_terms_acceptance')->insert([
      ['id' => 1, 'user_id' => 1, 'use_terms_id' => 1, 'accepted_at' => now()],
      ['id' => 2, 'user_id' => 2, 'use_terms_id' => 1, 'accepted_at' => now()],
      ['id' => 3, 'user_id' => 3, 'use_terms_id' => 1, 'accepted_at' => now()]
    ]);
  }
}
