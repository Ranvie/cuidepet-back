<?php

namespace App\Events;

use App\DTO\User\UserDTO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecoverPasswordEvent {
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Cria um evento para recuperação de senha, contendo as informações do usuário e a URL de redefinição de senha.
   * @param UserDTO $user     O usuário que solicitou a recuperação de senha
   * @param string  $resetUrl A URL para redefinição de senha
   */
  public function __construct(
    public readonly UserDTO $user,
    public readonly string $resetUrl,
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
