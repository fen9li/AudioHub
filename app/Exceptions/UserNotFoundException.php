<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    protected $message = 'No user found for the given request. Please register first. Thank you for using this application.';
}
