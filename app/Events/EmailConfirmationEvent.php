<?php

namespace App\Events;

use App\Models\UserModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmationEvent {

  /**
   * Traits
   */
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Cria um evento para disparar e-mail de confirmação, contendo as informações do usuário e a URL de confirmação.
   * @param UserModel $user            O usuário que solicitou confirmação de e-mail
   * @param string    $confirmationUrl A URL para confirmação do e-mail
   */
  public function __construct(
    public readonly UserModel $user,
    public readonly string    $confirmationUrl,
  ){}

  /**
   * Obtém os canais de transmissão para o evento. Neste caso, o evento é transmitido em um canal privado.
   * @return array Os canais de transmissão para o evento
   */
  public function broadcastOn(): array {
    return [
      new PrivateChannel('channel-name'),
    ];
  }
}
