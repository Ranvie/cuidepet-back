<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
  
  /**
   * Registra um serviço na aplicação.
   * @return void
   */
  public function register(): void {
    //
  }

  /**
   * Realiza ações após o registro dos serviços.
   * @return void
   */
  public function boot(): void {
    $this->registerRoutePatterns();
  }

  /**
   * Registra padrões de rota para parâmetros comuns.
   * Define expressões regulares para validar os parâmetros de rota, como IDs numéricos.
   * @return void
   */
  private function registerRoutePatterns(): void {
    $patterns = [
      'id'             => '[0-9]{1,18}',
      'userId'         => '[0-9]{1,18}',
      'announcementId' => '[0-9]{1,18}',
      'notificationId' => '[0-9]{1,18}',
      'formId'         => '[0-9]{1,18}',
      'type'           => 'lost|donation',
      'zipCode'        => '[0-9]{5}-?[0-9]{3}'
    ];

    foreach ($patterns as $key => $pattern) {
      Route::pattern($key, $pattern);
    }
  }
}
