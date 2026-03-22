<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecieSeeder extends Seeder {
  
  /**
   * Roda os seeders para popular a tabela de espécies.
   * @return void
   */
  public function run(): void {
    DB::table('tb_specie')->insert([
      ['id' => 1, 'name' => 'Cachorro'],
      ['id' => 2, 'name' => 'Gato'],
      ['id' => 3, 'name' => 'Pássaro'],
      ['id' => 4, 'name' => 'Peixe']
    ]);
  }
}
