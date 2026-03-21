<?php

namespace App\DTO\NotificationTemplate;

class NotificationTemplateDTO {

  /**
   * Identificador do template de notificação
   * @var int
   */
  public int $id;

  /**
   * Tipo do template de notificação
   * @var string
   */
  public string $type;

  /**
   * Título do template de notificação
   * @var string
   */
  public string $title;

  /**
   * Mensagem do template de notificação
   * @var string
   */
  public string $message;
}