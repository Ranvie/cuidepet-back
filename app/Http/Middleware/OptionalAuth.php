<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para autenticação opcional usando Sanctum.
 * Permite que rotas funcionem tanto para usuários autenticados quanto não autenticados.
 * Se um token válido for fornecido, o usuário será autenticado.
 * Caso contrário, a requisição prossegue sem autenticação.
 */
class OptionalAuth {
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    // Tenta autenticar usando Sanctum se um token for fornecido
    if ($request->bearerToken()) {
      try {
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        $user        = $accessToken?->tokenable;
        
        if ($user) {
          $request->setUserResolver(fn () => $user);
        }
      } catch (\Exception $e) {
        // Ignora erros de autenticação e continua como usuário não autenticado
      }
    }

    return $next($request);
  }
}
