<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ResourceThrottle {
  
  /** 
   * Intercepta a requisição e aplica a limitação de requisições.
   * @param  Request $request A requisição HTTP.
   * @param  Closure $next O próximo middleware na cadeia.
   * @return Response A resposta HTTP após o processamento.
   * @throws BusinessException Se o limite de requisições for excedido.
   */
  public function handle(Request $request, Closure $next, mixed ...$params): Response {
    $maxAttempts  = $params[0] ?? 60;
    $decayMinutes = $params[1] ?? 1;

    $key = sprintf(
      '%s:%s:%s',
      $request->ip(),
      $request->method(),
      $request->path()
    );

    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
      throw new BusinessException(
        'Muitas requisições. Por favor, tente novamente mais tarde.',
        429
      );
    }

    RateLimiter::hit($key, $decayMinutes * 60);

    return $next($request);
  }
}
