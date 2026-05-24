<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  
  /**
   * Roda os seeders para popular a tabela de raças.
   * @return void
   */
  public function run(): void {
    $this->call([
      UserSeeder::class,
      NotificationTemplateSeeder::class,
      PreferenceSeeder::class,
      RoleSeeder::class,
      UserRoleSeeder::class,
      UseTermsSeeder::class,
      UserTermsAcceptanceSeeder::class,
      SpecieSeeder::class,
      BreedSeeder::class,
      ReportMessageSeeder::class
    ]);
  }
}
