<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Classe responsável por popular a tabela de preferências com dados iniciais.
 */
class PreferenceSeeder extends Seeder {

  /**
   * Roda os seeders para popular a tabela de preferências.
   * @return void
   */
  public function run(): void {
    DB::table('tb_preference')->insert([
      ['user_id' => 1, 'receive_region_alarms' => false, 'receive_alarms_on_email' => false],
      ['user_id' => 2, 'receive_region_alarms' => false, 'receive_alarms_on_email' => false],
      ['user_id' => 3, 'receive_region_alarms' => false, 'receive_alarms_on_email' => false]
    ]);
  }
}
