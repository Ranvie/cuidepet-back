<?php

namespace App\Services;

use App\MessageDispatcher\Builders\EmailBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;
use App\Services\Interfaces\IContactUsService;

/**
 * Serviço para lidar com as requisições de contato.
 */
class ContactUsService implements IContactUsService {

  /**
   * Método para lidar com as requisições de contato.
   * @param  array $contactData
   * @return void
   */
  public function contactUs(array $contactData): void {
    $recipient = env('MAIL_FROM_ADDRESS');

    (new MessageDispatcher(
      new EmailBuilder([$recipient], 'Contato recebido', 'mail.contactUs', [
        'name'    => $contactData['name']    ?? '',
        'email'   => $contactData['email']   ?? '',
        'message' => $contactData['message'] ?? ''
      ])
    ))->dispatch();
  }

}