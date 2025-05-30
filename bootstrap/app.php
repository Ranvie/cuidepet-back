<?php

use App\Exceptions\BusinessExceptionHandler;
use App\Http\Middleware\CheckIfActionBelongsToUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'checkUser' => CheckIfActionBelongsToUser::class,
            'notHasRole' => \App\Http\Middleware\CheckIfUserDoNotHasRole::class,
            'hasRole' => \App\Http\Middleware\CheckIfUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $exception) {
            return (new BusinessExceptionHandler($exception))
                        ->render();
        });
    })->create();
