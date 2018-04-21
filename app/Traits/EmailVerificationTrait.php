<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\EmailVerificationLinkBrokenException;

trait EmailVerificationTrait
{
    /**
     * clear used token
     *
     * @param  string  $token
     * @return void
     */
    protected function clearUsedToken($email)
    {
        DB::table('email_verifications')
            ->where('email', $email)
            ->delete();
    }

    /**
     * mark user email as verified
     *
     * @param  App\User $user
     * @return void
     */
    protected function markUserEmailIsVerified($user)
    {
        $user->is_verified = true ;
        $user->verified_at = Carbon::now();
        $user->save();
    }

    /**
     * Get the guard to be used after activation succeeds.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * Validate email verification link.
     *
     * @param  Request $request
     * @return Response
     * @throws App\Exceptions\EmailVerificationLinkBrokenException
     *
     */
    protected function validateVerificationRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
           throw new EmailVerificationLinkBrokenException();
        }
    }
 
    /**
     * Get the user by e-mail.
     *
     * @param  string  $email
     * @return User $user
     *
     * @throws App\Exceptions\UserNotFoundException
     */
    protected function getUserByEmail($email)
    {
        $user = User::where('email', $email)->first();
        if ($user === null) {
            throw new UserNotFoundException();
        }
        return $user;
    }
 
    /**
     * Get the saved token by e-mail.
     *
     * @param  string $email
     * @return string $token
     *
     */
    protected function getSavedTokenByEmail($email)
    {
        $token = DB::table('email_verifications')
                 ->where('email', $email)
                 ->first(['token']);
        return $token->token;
    }

    /**
     * Update token in table email_verifications
     *
     * @param  string  $email, $token
     * @return void
     */
    protected function updateToken($email,$token)
    {
       // update record if exists
       // insert new record if does not exist
       $exists = DB::table('email_verifications')
                   ->where('email', $email)
                   ->first();
       if ( $exists ) {
          DB::table('email_verifications')
                   ->where('email', $email)
                   ->update(['token' => $token,
                             'created_at' => Carbon::now()]);
       } else {                
          DB::table('email_verifications')
                   ->insert([ 'email' => $email,
                              'token' => $token,
                              'created_at' => Carbon::now()]);
       }
    }

    /**
     * Generate email verification token.
     *
     * @return string|bool
     */
    protected function generateToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

}
