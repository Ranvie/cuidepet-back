<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UseTermsSeeder extends Seeder {
	
	/**
   * Roda os seeders para popular a tabela de termos de uso.
   * @return void
   */
	public function run(): void {

		$useTermsContent = file_get_contents(resource_path('documents/cuidepet-use-terms.html'));

		DB::table('tb_use_terms')->insert([
			[
				'id' => 1,
				'title' => 'Termos de uso do Usuário - CuidePet',
				'description' => $useTermsContent,
				'active' => true,
				'created_at' => now(),
				'updated_at' => now(),
			],
		]);
	}
}
