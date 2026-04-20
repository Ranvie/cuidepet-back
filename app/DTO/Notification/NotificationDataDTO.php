<?php

namespace App\DTO\Notification;

use App\Http\Enums\NotificationTypes;
/**
 * DTO para representar os dados de uma notificação, incluindo o tipo da notificação e a ação associada, que pode conter o nome da ação e seus parâmetros para personalizar o comportamento da notificação
 */
class NotificationDataDTO {

  /**
   * Tipo da notificação
   * @var NotificationTypes
   */
  public NotificationTypes $type;

  /**
   * Ação associada à notificação, contendo o nome da ação e seus parâmetros
   * @var NotificationDataDTOAction
   */
  public NotificationDataDTOAction $action;

}