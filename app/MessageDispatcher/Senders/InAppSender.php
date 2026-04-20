<?php

namespace App\MessageDispatcher\Senders;

use App\Exceptions\BusinessException;
use App\MessageDispatcher\Contracts\InAppMessage;
use App\MessageDispatcher\Contracts\Message;
use App\MessageDispatcher\Contracts\Sender;
use App\Services\NotificationService;

/**
 * Sender responsável por enviar mensagens para o canal in-app, utilizando o serviço de notificações para enviar notificações diretamente para os usuários dentro do aplicativo
 */
class InAppSender implements Sender {

  /**
   * Construtor do sender, que recebe o serviço de notificações para enviar notificações diretamente para os usuários dentro do aplicativo
   * @param NotificationService $notificationService O serviço de notificações utilizado para enviar notificações diretamente para os usuários dentro do aplicativo
   */
  public function __construct(
    private NotificationService $notificationService
  ) {}

  /**
   * Envia a mensagem de notificação in-app para os usuários destinatários, utilizando o serviço de notificações para enviar notificações diretamente para os usuários dentro do aplicativo
   * @param  Message $message  A mensagem de notificação a ser enviada, que deve implementar a interface InAppMessage para garantir que ela contenha os métodos necessários para fornecer o conteúdo, tipo e dados da notificação
   * @throws BusinessException Lança uma exceção do tipo BusinessException se a mensagem não for compatível com o canal in-app, ou seja, se ela não implementar a interface InAppMessage
   * @return void Não retorna nenhum valor, apenas executa o envio da mensagem de notificação in-app para os usuários destinatários
   */
  public function send(Message $message): void {
    if (!$message instanceof InAppMessage)
      throw new BusinessException('A mensagem não é compatível com o canal in-app.', 422);

    foreach ($message->getRecipients() as $userId) {
      $this->notificationService->sendNotification($userId, $message->getNotificationData());
    }
  }
}
