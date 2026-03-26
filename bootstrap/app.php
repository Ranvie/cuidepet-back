<?php

use App\Exceptions\BusinessExceptionHandler;
use App\Http\Middleware\CheckIfActionBelongsToUser;
use \App\Http\Middleware\RejectIfUserHasRole;
use \App\Http\Middleware\AllowIfUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
      'checkUser' => CheckIfActionBelongsToUser::class,
      'notHasRole' => RejectIfUserHasRole::class,
      'hasRole' => AllowIfUserHasRole::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (\Throwable $exception) {
      return (new BusinessExceptionHandler($exception))
        ->render();
    });
  })->create();
