<?php

namespace App\MessageDispatcher\Builders;

use App\Exceptions\BusinessException;
use App\MessageDispatcher\Contracts\Builder;
use App\MessageDispatcher\Messages\EMailMessage;
use App\MessageDispatcher\Senders\EmailSender;

/**
 * Builder específico para construção de mensagens de email
 */
class EmailBuilder implements Builder {
  
  /**
   * Método Construtor
   * @param array      $emails       Lista de emails dos destinatários da newsletter
   * @param string     $subject      Assunto da newsletter
   * @param string     $template     Template de email a ser utilizado para a newsletter
   * @param array      $templateData Dados a serem inseridos no template da newsletter
   * @param array|null $attachments  Lista de arquivos a serem anexados na newsletter
   */
  public function __construct(
    private array  $emails,
    private string $subject,
    private string $template,
    private array  $templateData = [],
    private array  $attachments = []
  ) {}

  /**
   * Constrói a mensagem de newsletter com base nas configurações definidas no builder, realizando as transformações ou validações necessárias antes do envio
   * @return EMailMessage Retorna a mensagem construída, que deve implementar a interface Message e conter
   */
  public function build(): EMailMessage {
    if(empty($this->emails))
      throw new BusinessException('Nenhum email destinatário fornecido para a newsletter.', 422);

    $templateExists = view()->exists($this->template);
    if (!$templateExists)
      throw new BusinessException("O template '{$this->template}' não existe.", 422);

    return new EMailMessage(
      $this->emails,
      $this->subject,
      $this->template,
      $this->templateData,
      $this->attachments
    );
  }

  /**
   * Retorna o sender associado a este builder, que será responsável por enviar a mensagem construída
   * @return EmailSender O sender associado a este builder
   */
  public function getSender(): EmailSender {
    return app(EmailSender::class);
  }
}