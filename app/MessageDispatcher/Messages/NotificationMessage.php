<?php

namespace App\MessageDispatcher\Messages;

use App\DTO\Notification\NotificationDataDTO;
use App\Http\Enums\NotificationTypes;
use App\MessageDispatcher\Contracts\Message;
use App\MessageDispatcher\Contracts\InAppMessage;

/**
 * Classe de mensagem para notificações, implementando as interfaces Message e InAppMessage, permitindo enviar notificações para usuários específicos com um tipo e dados associados
 */
class NotificationMessage implements Message, InAppMessage {

  /**
   * Construtor da mensagem de notificação
   * @param array                    $userIds Array de IDs dos usuários destinatários da notificação
   * @param NotificationTypes        $type    Tipo da notificação
   * @param NotificationDataDTO|null $data    Dados adicionais para a notificação, que podem ser usados para personalizar o conteúdo da notificação
   */
  public function __construct(
    private array                $userIds,
    private ?NotificationDataDTO $data = null,
  ) {}

  /**
   * Retorna os IDs dos usuários destinatários da notificação
   * @return array Retorna um array contendo os IDs dos usuários destinatários da notificação
   */
  public function getRecipients(): array {
    return $this->userIds;
  }

  /**
   * Retorna os dados adicionais para a notificação, que podem ser utilizados para personalizar o conteúdo da notificação ou fornecer informações adicionais para o processamento da mensagem
   * @return NotificationDataDTO|null Retorna um objeto NotificationDataDTO contendo os dados adicionais para a notificação, ou null se não houver dados adicionais
   */
  public function getNotificationData(): ?NotificationDataDTO {
    return $this->data;
  }
}
