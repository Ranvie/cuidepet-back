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
   * @param array  $emails      Lista de emails dos destinatários do email
   * @param string $subject     Assunto do email
   * @param string $template    Template de email a ser utilizado para o email
   * @param array  $data        Dados a serem inseridos no template do email
   * @param array  $attachments Lista de arquivos a serem anexados no email
   */
  public function __construct(
    private array  $emails,
    private string $subject,
    private string $template,
    private array  $data = [],
    private array  $attachments = [],
  ) {}

  /**
   * Retorna a lista de destinatários do email, que são os emails fornecidos na construção da mensagem
   * @return array Lista de emails dos destinatários do email
   */
  public function getRecipients(): array {
    return $this->emails;
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
