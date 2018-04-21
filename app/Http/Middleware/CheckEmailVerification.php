<?php

namespace App\Http\Middleware;

use Closure;

use App\User;
use App\Exceptions\UserNotFoundException;
use App\Traits\EmailVerificationTrait;
use App\Notifications\EmailVerificationNotification;

class CheckEmailVerification
{
    use EmailVerificationTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // find the user by user email in login form
        $user = User::where('email', $request->email)->first();
        if ($user === null) { throw new UserNotFoundException(); }

        if ($user->is_verified == false) {
           
           // generate email verification token
           $token = $this->generateToken();

           // save email verification token to table email_verifications
           $this->updateToken($user->email,$token);    

           // send email verification token link email
           $user->notify(new EmailVerificationNotification($user->email,$token));

           return redirect('/')->with('message','We have send you the email verification link. Please verify your email first.Thank you for using this application.');
        }

        return $next($request);
    }
}
