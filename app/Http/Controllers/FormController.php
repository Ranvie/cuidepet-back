<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use App\Services\FormService;
use Illuminate\Http\JsonResponse;

class FormController {

  /**
   * Método Construtor
   * @param FormService $obFormService Serviço responsável pelas operações relacionadas a formulários
   */
  public function __construct(
    private FormService $obFormService
  ) {}

  /**
   * Lista os formulários de um usuário específico
   * @param int $userId ID do usuário para o qual os formulários serão listados
   * @return JsonResponse Resposta JSON contendo a lista de formulários do usuário
   */
  public function list(int $userId): JsonResponse {
    $registers = $this->obFormService->listFormByUser($userId);

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }

  public function get(int $userId) {
    // TODO: Implement get() method.
  }

  /**
   * Cria um novo formulário para um usuário específico
   * @param  int             $userId  ID do usuário para o qual o formulário será criado
   * @param  UserFormRequest $request Objeto de requisição contendo os dados do formulário a ser criado
   * @return JsonResponse             Resposta JSON contendo os detalhes do formulário criado
   */
  public function create(int $userId, UserFormRequest $request): JsonResponse {
    $obFormRequest = array_merge($request->validated(), ['userId' => $userId]);
    $registers = $this->obFormService->create($obFormRequest);
    return new BusinessResponse(200, $registers)->build();
  }

  public function update(int $userId, UserRequest $request) {
    // TODO: Implement update() method.
  }

  public function delete(int $userId) {
    // TODO: Implement delete() method.
  }
}
