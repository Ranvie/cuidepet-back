<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Services\UseTermsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptTerms {

  /**
   * Método Construtor
   */
  public function __construct(
    private UseTermsService $useTermsService
  ) {}
  
  /**
   * Verifica se o usuário autenticado aceitou os termos de uso antes de permitir o acesso aos recursos protegidos. Se o usuário não tiver aceitado os termos, uma exceção de negócio é lançada, indicando que o acesso é proibido.
   * @param  Request $request  Request HTTP contendo os dados da requisição.
   * @param  Closure $next     Closure que representa a próxima etapa do processamento da requisição.
   * @return Response          retorna a resposta da próxima etapa do processamento da requisição se a verificação for bem-sucedida.
   * @throws BusinessException lançada quando o ID do token não corresponde ao ID presente na rota, indicando que o usuário não tem permissão para realizar a ação.
   */
  public function handle(Request $request, Closure $next): Response {
    $user               = $request->user();
    $useTermsIsAccepted = $this->useTermsService->validateUseTermsAcceptance($user?->id);

    if (!$useTermsIsAccepted) {
      throw new BusinessException('Termos de uso não aceitos', 403, [
        'useTermsAcceptance' => false
      ]);
    }

    return $next($request);
  }
}
