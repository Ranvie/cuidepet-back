<?php

namespace App\DTO\User;

use App\DTO\Notification\NotificationDTO;
use Illuminate\Support\Collection;

class SafeUserDTO{

  /**
   * Identificador do usuário
   * @var int
   */
  public int $id;

  /**
   * Nome de usuário
   * @var string
   */
  public string $username;

  /**
   * Imagem de perfil do usuário
   * @var string|null
   */
  public ?string $imageProfile;

  /**
   * Notificações do usuário
   * @var Collection<NotificationDTO>
   */
  public Collection $notifications;
}