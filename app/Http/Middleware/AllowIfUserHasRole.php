<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowIfUserHasRole {
  
  /**
   * Valida se o usuário autenticado possui as habilidades (roles) especificadas nos parâmetros.
   * @param  Request $request  Request HTTP contendo os dados da requisição.
   * @param  Closure $next     Closure que representa a próxima etapa do processamento da requisição.
   * @param  mixed ...$params  Habilidades (roles) a serem verificadas.
   * @throws BusinessException lançada quando o token do usuário não possui as habilidades especificadas, indicando que o token é inválido ou não tem as permissões necessárias.
   * @return Response          Retorna a resposta da próxima etapa do processamento da requisição se a verificação for bem-sucedida.
   */
  public function handle(Request $request, Closure $next, mixed ...$params): Response {
    $permitted = false;
    $user      = $request->user();
    $abilities = $user?->currentAccessToken()?->abilities ?? [];

    foreach ($params as $param) {
      if (\in_array($param, $abilities, true)) {
        $permitted = true;
        break;
      }
    }

    if (!$permitted) {
      throw new BusinessException('Token inválido ou sem as permissões necessárias', 401);
    }

    return $next($request);
  }
}
