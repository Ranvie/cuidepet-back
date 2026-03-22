<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfActionBelongsToUser {
  
  /**
   * Verifica se a ação pertence ao usuário autenticado, comparando o ID do token com o ID presente na rota.
   * @param  Request $request  Request HTTP contendo os dados da requisição.
   * @param  Closure $next     Closure que representa a próxima etapa do processamento da requisição.
   * @return Response          retorna a resposta da próxima etapa do processamento da requisição se a verificação for bem-sucedida.
   * @throws BusinessException lançada quando o ID do token não corresponde ao ID presente na rota, indicando que o usuário não tem permissão para realizar a ação.
   */
  public function handle(Request $request, Closure $next): Response {
    $userTokenId = $request->user()?->getAuthIdentifier();
    if ($userTokenId === null) {
      throw new BusinessException('Usuário não autenticado.', 401);
    }

    $userRouteId = $request->route('userId');
    if ($userRouteId != $userTokenId) {
      throw new BusinessException('O usuário não possui a permissão para isso.', 403);
    }

    return $next($request);
  }
}
