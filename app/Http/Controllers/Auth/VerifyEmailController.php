<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\EmailVerificationTrait;
use App\Exceptions\EmailVerificationLinkBrokenException;

class VerifyEmailController extends Controller
{
    use EmailVerificationTrait;

    /**
     * Handle the email verification request
     *
     * Illuminate\Http\Request $request
     * @return Response
     *
     * @throws App\Exceptions\TokenMismatchException
     */
    public function verifyEmail(Request $request)
    {
        $this->validateVerificationRequest($request);

//dd($request);

        //get the request token
        $requestSegments = $request->segments();
        array_shift($requestSegments);
        $requestToken = array_shift($requestSegments);
        //get the user requests verification
        $user = $this->getUserByEmail($request->input('email'));

        //get the saved token for this user
        $savedToken = $this->getSavedTokenByEmail($request->input('email'));
        if ($requestToken == $savedToken) {
            $this->markUserEmailIsVerified($user);
            $this->clearUsedToken($request->input('email'));
            $this->guard()->login($user, 'true');
            return redirect('/home')->with('success','Your email has been verified successfully. Thank you for using this application!');
        } else {
            throw new EmailVerificationLinkBrokenException();
        }
    }
}
