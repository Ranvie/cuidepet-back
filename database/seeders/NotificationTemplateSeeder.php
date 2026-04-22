<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplateSeeder extends Seeder {
	
	/**
   * Roda os seeders para popular a tabela de templates de notificação.
   * @return void
   */
	public function run(): void {
		DB::table('tb_notification_template')->insert([
			['id' => 1, 'type' => 'welcome', 'title' => 'Bem-vindo(a)!', 'message' => 'Seja bem-vindo(a) ao CuidePet! Crie seu anúncio clicando aqui.'],
			['id' => 2, 'type' => 'announcement-alert', 'title' => 'Há um novo anúncio de pet {type} na sua região!', 'message' => 'Você pode conferir os detalhes clicando aqui.'],
			['id' => 3, 'type' => 'new-response', 'title' => 'Você obteve uma nova resposta!', 'message' => 'Há uma nova resposta para o seu anúncio do pet {petName}. Clique aqui para ver os detalhes.'],
			['id' => 4, 'type' => 'announcement-update', 'title' => 'Nova Atualização!', 'message' => 'Um anúncio que você favoritou foi alterado. Confira clicando aqui.'],
			['id' => 5, 'type' => 'pet-found', 'title' => 'Pet Encontrado!', 'message' => 'O anúncio do pet {petName} foi atualizado para "Encontrado".'],
			['id' => 6, 'type' => 'pet-adopted', 'title' => 'Pet Doado!', 'message' => 'O pet {petName} que você estava acompanhando foi adotado.'],
			['id' => 7, 'type' => 'favorited-announcement-paused', 'title' => 'Anúncio Pausado!', 'message' => 'O anúncio do pet {petName} que você estava acompanhando foi pausado.'],
			['id' => 8, 'type' => 'announcement-paused', 'title' => 'Seu anúncio foi Pausado!', 'message' => 'O anúncio do seu pet {petName} foi pausado devido a muitas denúncias.'],
			['id' => 9, 'type' => 'form-paused', 'title' => 'Seu formulário foi Pausado!', 'message' => 'O seu formulário {title} foi pausado devido a muitas denúncias.'],
		]);
	}
}
