<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiResponse::notFound(__('errors.not_found'));
            }
        });

        $exceptions->render(function (Illuminate\Database\Eloquent\ModelNotFoundException $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiResponse::notFound(__('errors.model_not_found'));
            }
        });

        $exceptions->render(function (Illuminate\Validation\ValidationException $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiResponse::validationError($e->errors(), __('errors.validation_failed'));
            }
        });

        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiResponse::error('شما وارد نشده‌اید. لطفاً ابتدا وارد شوید.', 401);
            }
        });
    })->create();
