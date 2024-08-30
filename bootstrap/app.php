<?php

use App\Http\Middleware\ForceJsonResponseMiddleware;
use App\Http\Middleware\JwtRoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [ForceJsonResponseMiddleware::class]);
        $middleware->alias(['role' => JwtRoleMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception) {
            return jsonResponse(
                status: 422,
                message: 'Validation errors',
                errors: $exception->errors()
            );
        });

        $exceptions->render(function (AuthenticationException $exception) {
            return jsonResponse(status: 401, message: $exception->getMessage());
        });

        $exceptions->render(function (NotFoundHttpException $exception) {
            return jsonResponse(status: 404, message: $exception->getMessage());
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception) {
            return jsonResponse(status: 405, message: $exception->getMessage());
        });

    })->create();
