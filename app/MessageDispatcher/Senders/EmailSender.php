<?php

namespace App\MessageDispatcher\Senders;

use App\Exceptions\BusinessException;
use App\Mail\EmailTemplate;
use App\MessageDispatcher\Contracts\EmailableMessage;
use App\MessageDispatcher\Contracts\Message;
use App\MessageDispatcher\Contracts\Sender;
use Illuminate\Support\Facades\Mail;

/**
 * Classe responsável por enviar mensagens do tipo email, verificando se a mensagem suporta o canal de email e utilizando o sistema de filas do Laravel para enviar os emails de forma assíncrona
 */
class EmailSender implements Sender {

  /**
   * Envia a mensagem de email, verificando se a mensagem é compatível com o canal de email e utilizando o sistema de filas do Laravel para enviar os emails de forma assíncrona
   * @param Message $message A mensagem a ser enviada, que deve implementar a interface EmailableMessage e conter os dados necessários para o envio do email
   * @return void
   */
  public function send(Message $message): void {
    if (!$message instanceof EmailableMessage)
      throw new BusinessException('A mensagem não é compatível com o canal de email.', 422);

    $delaySeconds = 0;
    foreach ($message->getRecipients() as $recipient) {
      $mergedData = array_merge($message->getData(), $recipient['vars']);

      $mailInfo = new EmailTemplate(
        $message->getSubject(),
        $message->getTemplate(),
        $mergedData,
        $message->getAttachments()
      );

      Mail::to($recipient['email'])->later(now()->addSeconds($delaySeconds), $mailInfo);
      $delaySeconds += config('mail.delay', 30);
    }
  }
}
