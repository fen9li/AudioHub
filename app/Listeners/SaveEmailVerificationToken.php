<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Traits\EmailVerificationTrait;

class SaveEmailVerificationToken
{
    use EmailVerificationTrait;

    private $user;

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
        // setup user
        $this->user = $event->user;

        // generate email verification token
        $token = $this->generateToken();

        // save email verification token to table email_verifications
        $this->updateToken($this->user->email,$token);      
    }
}
