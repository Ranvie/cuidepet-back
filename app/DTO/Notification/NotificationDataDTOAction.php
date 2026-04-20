<?php

namespace App\DTO\Notification;

/**
 * Classe para representar a ação associada a uma notificação, contendo o nome da ação e seus parâmetros
 */
class NotificationDataDTOAction {
  
  /**
   * Nome da ação a ser realizada quando a notificação for clicada, como 'announcement.view' ou 'none' para ações sem interação
   * @var string
   */
  public string $name;

  /**
   * Parâmetros adicionais para a ação, que podem incluir IDs de recursos ou outras informações necessárias para executar a ação corretamente
   * @var array
   */
  public array $parameters;

}