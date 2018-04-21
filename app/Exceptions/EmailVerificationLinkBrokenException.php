<?php

namespace App\Exceptions;

use Exception;

class EmailVerificationLinkBrokenException extends Exception
{
    protected $message = 'The email verification link is broken. Enter your registered email, and press Login, you will receive another email verification link. Thank you for using this application.';
}
