<?php

namespace App\DTO\User;

use App\DTO\Notification\NotificationDTO;
use Illuminate\Support\Collection;

/**
 * Data Transfer Object (DTO) para representar os dados de um usuário sem informações sensíveis, como email e telefone.
 */
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
   * Email do usuário
   * @var string|null
   */
  public ?string $email;

  /**
   * Telefone do usuário
   * @var string|null
   */
  public ?string $phone;

  /**
   * Imagem de perfil do usuário
   * @var string|null
   */
  public ?string $imageProfileUrl;

  /**
   * Notificações do usuário
   * @var Collection<NotificationDTO>
   */
  public Collection $notifications;

  /**
   * Data de expiração do token de acesso do usuário
   * @var string|null
   */
  public ?string $tokenExpiresAt;
}