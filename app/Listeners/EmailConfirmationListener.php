<?php

namespace App\Listeners;

use App\Events\EmailConfirmationEvent;
use App\Mail\EmailConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailConfirmationListener implements ShouldQueue {

  /**
   * Envia o email de recuperação de senha para a fila de envio
   * @param  EmailConfirmationEvent $event
   * @return void
   */
  public function handle(EmailConfirmationEvent $event): void {
    $mail = new EmailConfirmationMail(
      $event->user->username,
      $event->user->email,
      $event->confirmationUrl
    );

    Mail::to($event->user)->queue($mail);
  }
  
}
