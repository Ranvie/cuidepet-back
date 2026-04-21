<?php

namespace App\MessageDispatcher\Messages;

use App\MessageDispatcher\Contracts\Message;
use App\MessageDispatcher\Contracts\EmailableMessage;

/**
 * Classe de mensagem específica para emails, implementando as interfaces Message e EmailableMessage para garantir a estrutura necessária para envio de emails
 */
class EmailMessage implements Message, EmailableMessage {

  /**
   * Método Construtor
   * @param array  $recipients  Lista de destinatários do email. Cada item pode ser uma string (email) ou um array com 'email' e 'vars' (dados individuais do destinatário)
   * @param string $subject     Assunto do email
   * @param string $template    Template de email a ser utilizado para o email
   * @param array  $data        Dados compartilhados a serem inseridos no template do email (mesmos para todos os destinatários)
   * @param array  $attachments Lista de arquivos a serem anexados no email
   */
  public function __construct(
    private array  $recipients,
    private string $subject,
    private string $template,
    private array  $data = [],
    private array  $attachments = [],
  ) {}

  /**
   * Retorna a lista de destinatários do email no formato estruturado ['email' => string, 'vars' => array]
   * @return array Lista de destinatários estruturados
   */
  public function getRecipients(): array {
    return array_map(function ($recipient) {
      if (is_string($recipient))
        return ['email' => $recipient, 'vars' => []];

      return $recipient;
    }, $this->recipients);
  }

  /**
   * Retorna o assunto do email, que é o assunto fornecido na construção da mensagem
   * @return string Assunto do email
   */
  public function getSubject(): string {
    return $this->subject;
  }

  /**
   * Retorna o template do email, que é o template fornecido na construção da mensagem
   * @return string Template do email
   */
  public function getTemplate(): string {
    return $this->template;
  }

  /**
   * Retorna os dados a serem inseridos no template do email, que são os dados fornecidos na construção da mensagem
   * @return array Dados a serem inseridos no template do email
   */
  public function getData(): array {
    return $this->data;
  }

  /**
   * Retorna a lista de arquivos a serem anexados no email, que são os arquivos fornecidos na construção da mensagem
   * @return array Lista de arquivos a serem anexados no email
   */
  public function getAttachments(): array {
    return $this->attachments;
  }
}
