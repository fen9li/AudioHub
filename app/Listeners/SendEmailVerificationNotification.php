<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Notifications\EmailVerificationNotification;
use App\Traits\EmailVerificationTrait;

class SendEmailVerificationNotification
{
    use EmailVerificationTrait;

    private $token;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegisteredEvent  $event
     * @return void
     */
    public function handle(UserRegisteredEvent $event)
    {
        // get email verification token 
        $this->token = $this->getSavedTokenByEmail($event->user->email);

        // send email verification token link email
        $event->user->notify(new EmailVerificationNotification($event->user->email,$this->token));
    }
}
