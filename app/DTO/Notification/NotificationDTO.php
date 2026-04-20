<?php

namespace App\DTO\Notification;

use App\DTO\NotificationTemplate\NotificationTemplateDTO;
use App\DTO\User\UserDTO;

/**
 * Data Transfer Object (DTO) para representar os dados de uma notificação.
 */
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
   * @var NotificationDataDTO|string|null
   */
  public NotificationDataDTO|string|null $data;

  /**
   * Data de criação da notificação
   * @var string
   */
  public string $createdAt;
}
