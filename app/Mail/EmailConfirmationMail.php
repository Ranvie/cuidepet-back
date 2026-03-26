<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailConfirmationMail extends Mailable {

  /**
   * Traits
   */
  use Queueable, SerializesModels;

  /**
   * Cria uma nova instância da mensagem de email de confirmação.
   * @param string $username        Nome de usuário do destinatário
   * @param string $email           Endereço de email do destinatário
   * @param string $confirmationUrl URL para a página de redefinição de senha
   */
  public function __construct(
    public string $username,
    public string $email,
    public string $confirmationUrl,
  ) {}

  /**
   * Obtem o envelope da mensagem.
   * @return Envelope O envelope da mensagem, contendo o assunto do email
   */
  public function envelope(): Envelope {
    return new Envelope(
      subject: 'Cuide Pet - Confirmação de Email',
    );
  }

  /**
   * Obtem o conteúdo da mensagem.
   * @return Content O conteúdo da mensagem, especificando o template markdown a ser usado
   */
  public function content(): Content {
    return new Content(
      markdown: 'mail.emailConfirmation',
    );
  }

  /**
   * Obtem os anexos da mensagem.
   * @return array Uma matriz de anexos, que neste caso é vazia
   */
  public function attachments(): array {
    return [];
  }
}
