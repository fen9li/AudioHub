<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait EmailVerificationTrait
{
  
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
