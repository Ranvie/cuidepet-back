<?php

namespace App\Listeners;

use App\Events\RecoverPasswordEvent;
use App\Mail\RecoverPasswordMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailConfirmationListener implements ShouldQueue {

  /**
   * Envia o email de recuperação de senha para a fila de envio
   * @param  RecoverPasswordEvent $event
   * @return void
   */
  public function handle(RecoverPasswordEvent $event): void {
    $mail = new RecoverPasswordMail(
      $event->user->username,
      $event->user->email,
      $event->resetUrl
    );

    Mail::to($event->user)->queue($mail);
  }
  
}
