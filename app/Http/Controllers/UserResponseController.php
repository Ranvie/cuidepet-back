<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingRequest;
use App\Http\Response\BusinessResponse;
use App\Services\UserResponseService;
use Illuminate\Http\JsonResponse;

/**
 * Controlador responsável por lidar com as requisições relacionadas às respostas dos usuários.
 * Fornece métodos para listar, obter detalhes e excluir respostas dos usuários.
 */
class UserResponseController {
  
  /**
   * Método Construtor
   * @param UserResponseService $obUserResponseService Instância do serviço de resposta de usuário para manipular as respostas dos usuários.
   */
  public function __construct(
    private UserResponseService $obUserResponseService
  ) {}

  /**
   * Listagem de resposta do usuário
   * @param  ListingRequest $request Request de listagem para parâmetros de paginação
   * @return JsonResponse            Resposta JSON do serviço.
   */
  public function list(ListingRequest $request) :JsonResponse {
    $validated = $request->validated();
    $registers = $this->obUserResponseService->listUserResponses($validated['limit'], $validated['page'], auth()->id());

    return new BusinessResponse(200, $registers)->build();
  }

  /**
   * Busca de resposta de um usuário
   * @param  int $responseId ID da resposta a ser consultada
   * @return JsonResponse    Resposta JSON do serviço.
   */
  public function get(int $responseId) :JsonResponse {
    $register = $this->obUserResponseService->getuserResponseById($responseId);

    return new BusinessResponse(200, $register)->build();
  }

  /**
   * Exclui uma resposta de um usuário
   * @param  int $responseId ID da resposta que deve ser excluída
   * @return JsonResponse
   */
  public function delete(int $responseId) :JsonResponse {
    $this->obUserResponseService->remove($responseId);
    return new BusinessResponse(200, 'Resposta excluída com sucesso')->build();
  }
}
