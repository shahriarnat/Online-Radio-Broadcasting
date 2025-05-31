<?php

use App\Http\Middleware\VisitorsMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use app\Helpers\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {

        /*
         Handle the authentication exception.
         This will return a JSON response with a 401 status code
         and the message 'Invalid token' when the user is not authenticated.
         */
        $exceptions->render(
            function (AuthenticationException $exception, Request $request) {
                return ApiResponse::error(
                    __('auth.invalid_token'),
                    Response::HTTP_UNAUTHORIZED
                );
            }
        );

        /*
         Handle the validation exception.
         This will return a JSON response with a 422 status code
         and the validation errors when the request fails validation.
         */
        $exceptions->render(
            function (ValidationException $exception, Request $request) {
                return ApiResponse::validation(
                    $exception->validator->errors(),
                    __('validation.validation_message'),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        );

    })->create();
