<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Server\Exception\OAuthServerException;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(\Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, \Throwable $exception)
    {
        if($exception instanceof NotFoundException) {
            return response()->json([
                'message' => $exception->getMessage()
            ], $exception->getCode());
        }

        if ($exception instanceof \Illuminate\Validation\UnauthorizedException)
        {
            return response()->json([
                'message' => 'You do not have access to do that.'
            ], 403);
        }

        if ($exception instanceof AuthorizationException || $exception instanceof AuthenticationException)
        {
            \Log::debug('oauth exception');
            return response()->json([
                'message' => 'You do not have access to do that.'
            ], 401);
        }

        Log::error($exception);

//        return response()->json([
//            'message' => 'Something went wrong.'
//        ], 500);

        return parent::render($request, $exception);
    }
}
