<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Services\UseTermsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUser {

  /**
   * Método Construtor
   */
  public function __construct(
    private UseTermsService $useTermsService
  ) {}

  /**
   * Blocklist de ações que exigem verificação de pertencimento ao usuário.
   * @var array  Lista de ações que exigem verificação de pertencimento ao usuário.
   */
  private const array BLOCKED_ROLES = [
    'confirm-email',
    'reset-password',
  ];
  
  /**
   * Verifica se a ação pertence ao usuário autenticado, comparando o ID do token com o ID presente na rota.
   * @param  Request $request  Request HTTP contendo os dados da requisição.
   * @param  Closure $next     Closure que representa a próxima etapa do processamento da requisição.
   * @return Response          retorna a resposta da próxima etapa do processamento da requisição se a verificação for bem-sucedida.
   * @throws BusinessException lançada quando o ID do token não corresponde ao ID presente na rota, indicando que o usuário não tem permissão para realizar a ação.
   */
  public function handle(Request $request, Closure $next): Response {
    $user               = $request->user();
    $abilities          = $user?->currentAccessToken()?->abilities ?? [];
    $useTermsIsAccepted = $this->useTermsService->validateUseTermsAcceptance($user?->id);

    if(!$useTermsIsAccepted) {
      throw new BusinessException('Termos de uso não aceitos', 403);
    }

    foreach (self::BLOCKED_ROLES as $role) {
      if (\in_array($role, $abilities, true)) {
        throw new BusinessException('Token inválido ou sem as permissões necessárias', 401);
      }
    }

    return $next($request);
  }
}
