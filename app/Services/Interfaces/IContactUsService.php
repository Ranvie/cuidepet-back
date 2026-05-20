<?php

namespace App\Services\Interfaces;

/**
 * Interface para o serviço de contato.
 */
interface IContactUsService {
  
  /**
   * Método para lidar com as requisições de contato.
   * @param array $contactData
   * @return void
   */
  public function contactUs(array $contactData) :void;
}