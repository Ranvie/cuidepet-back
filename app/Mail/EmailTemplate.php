<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Classe de email para envio de emails
 */
class EmailTemplate extends Mailable {

  use Queueable, SerializesModels;

  /**
   * Construtor do email
   * @param string $mailSubject     Assunto do email
   * @param string $template        Template markdown do email
   * @param array  $data            Dados para o template
   * @param array  $mailAttachments Lista de arquivos a serem anexados no email
   */
  public function __construct(
    public string $mailSubject,
    public string $template,
    public array  $data = [],
    public array  $mailAttachments = []
  ) {}

  /**
   * Define o envelope do email, incluindo o assunto
   * @return Envelope
   */
  public function envelope() :Envelope {
    return new Envelope(
      subject: $this->mailSubject,
    );
  }

  /**
   * Define o conteúdo do email, usando um template markdown
   * @return Content
   */
  public function content() :Content {
    return new Content(
      markdown: $this->template,
      with: $this->data
    );
  }

  /**
   * Define os anexos do email
   * @return array
   */
  public function attachments(): array {
    return $this->mailAttachments;
  }
}
