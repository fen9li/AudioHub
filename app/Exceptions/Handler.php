<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

<<<<<<< HEAD
=======
use App\Exceptions\UserNotFoundException;
use App\Exceptions\EmailVerificationLinkBrokenException;

>>>>>>> hotfix
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
<<<<<<< HEAD
=======
        if ($exception instanceof UserNotFoundException) {
            return redirect('/register')->with('message',$exception->getMessage()); }
        if ($exception instanceof EmailVerificationLinkBrokenException) {
            return redirect('/login')->with('message',$exception->getMessage()); }

>>>>>>> hotfix
        return parent::render($request, $exception);
    }
}
