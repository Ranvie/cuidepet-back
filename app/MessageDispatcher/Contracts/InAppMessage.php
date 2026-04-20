<?php

namespace App\MessageDispatcher\Contracts;

use App\DTO\Notification\NotificationDataDTO;

/**
 * Define o contrato para mensagens de notificação in-app, garantindo que elas implementem os métodos necessários para fornecer o conteúdo, tipo e dados da notificação
 */
interface InAppMessage {

  /**
   * Retorna os dados adicionais para a notificação, que podem ser utilizados para personalizar o conteúdo da notificação ou fornecer informações adicionais para o processamento da mensagem
   * @return NotificationDataDTO|null Retorna um objeto NotificationDataDTO contendo os dados adicionais para a notificação, ou null se não houver dados adicionais
   */
  public function getNotificationData(): ?NotificationDataDTO;
  
}
