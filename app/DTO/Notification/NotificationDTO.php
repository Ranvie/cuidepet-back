<?php

namespace App\DTO\Notification;

use App\DTO\NotificationTemplate\NotificationTemplateDTO;
use App\DTO\User\UserDTO;

class NotificationDTO {

  /**
   * Identificador da notificação
   * @var int
   */
  public int $id;

  /**
   * Objeto do usuário destinatário da notificação
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Objeto do template da notificação
   * @var NotificationTemplateDTO
   */
  public NotificationTemplateDTO $notificationTemplate;

  /**
   * Indica se a notificação foi visualizada
   * @var bool
   */
  public bool $viewed;

  /**
   * Dados adicionais da notificação
   * @var array|null
   */
  public ?array $data;

  /**
   * Data de criação da notificação
   * @var string
   */
  public string $createdAt;
}
