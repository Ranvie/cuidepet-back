<?php

namespace App\Listeners;

use App\Events\RecoverPasswordEvent;
use App\Mail\RecoverPasswordMail;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EmailUserAboutPasswordRecovery implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private UserService $userService
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecoverPasswordEvent $event): void
    {
        $mail = new RecoverPasswordMail(
            $event->user->username,
            $event->user->email,
            $event->resetUrl
        );

        Mail::to($event->user)->queue($mail);
    }
}
