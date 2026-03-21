<?php

namespace App\DTO\User;

use App\DTO\Notification\NotificationDTO;

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
   * @var string
   */
  public string $imageProfile;

  /**
   * Notificações do usuário
   * @var NotificationDTO[]
   */
  public array $notifications;
}