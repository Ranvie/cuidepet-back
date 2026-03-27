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
   * Lista os formulários do usuário autenticado
   * @return JsonResponse Resposta JSON contendo a lista de formulários do usuário
   */
  public function list(): JsonResponse {
    $registers = $this->obFormService->listFormByUser(auth()->id());

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }

  public function get(int $formId) {
    // TODO: Implement get() method.
  }

  /**
   * Cria um novo formulário para o usuário autenticado
   * @param  UserFormRequest $request Objeto de requisição contendo os dados do formulário a ser criado
   * @return JsonResponse             Resposta JSON contendo os detalhes do formulário criado
   */
  public function create(UserFormRequest $request): JsonResponse {
    $obFormRequest = array_merge($request->validated(), ['userId' => auth()->id()]);
    $registers = $this->obFormService->create($obFormRequest);
    return new BusinessResponse(200, $registers)->build();
  }

  public function update(int $formId, UserRequest $request) {
    // TODO: Implement update() method.
  }

  public function delete(int $formId) {
    // TODO: Implement delete() method.
  }
}
