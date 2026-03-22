<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportMessageSeeder extends Seeder {
	
	/**
   * Roda os seeders para popular a tabela de mensagens de denúncia.
   * @return void
   */
	public function run(): void {
		DB::table('tb_report_message')->insert([
			// DENÚNCIAS DE ANÚNCIO
			['id' => 1, 'type' => 'announcement', 'motive' => 'Informações falsas ou enganosas'],
			['id' => 2, 'type' => 'announcement', 'motive' => 'Conteúdo ofensivo ou impróprio'],
			['id' => 3, 'type' => 'announcement', 'motive' => 'Tentativa de golpe ou fraude'],
			['id' => 4, 'type' => 'announcement', 'motive' => 'Imagens enganosas ou impróprias'],
			['id' => 5, 'type' => 'announcement', 'motive' => 'Categoria de anúncio incorreta'],
			['id' => 6, 'type' => 'announcement', 'motive' => 'Anúncio duplicado'],
			['id' => 7, 'type' => 'announcement', 'motive' => 'Outros (descreva abaixo)'],
			// DENÚNCIAS DE FORMULÁRIO
			['id' => 8, 'type' => 'form', 'motive' => 'Perguntas irrelevantes /sem relação à pets'],
			['id' => 9, 'type' => 'form', 'motive' => 'Conteúdo ofensivo ou impróprio'],
			['id' => 10, 'type' => 'form', 'motive' => 'Tentativa de golpe ou fraude'],
			['id' => 11, 'type' => 'form', 'motive' => 'Solicitação de dados sensíveis'],
			['id' => 12, 'type' => 'form', 'motive' => 'Outros (descreva abaixo)']
		]);
	}
}
