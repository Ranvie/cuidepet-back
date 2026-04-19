<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingRequest;
use App\Http\Requests\UserFormRequest;
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
   * Lista paginada de formulários de um usuário específico.
   * @param  ListingRequest $request Objeto de requisição contendo os parâmetros de paginação (limit e page)
   * @return JsonResponse            Resposta JSON do serviço.
   */
  public function list(ListingRequest $request) :JsonResponse {
    $validated = $request->validated();
    $registers = $this->obFormService->listFormsByUser($validated['limit'], $validated['page'], auth()->id());

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }

  /**
   * Lista todos os formulários de um usuário específico, sem paginação. (para select de formulário)
   * @return JsonResponse Resposta JSON do serviço.
   */
  public function listAll() :JsonResponse {
    $registers = $this->obFormService->listAllUserForms(auth()->id());

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }

  /**
   * Busca formulário de um usuário por ID.
   * @param int $formId ID do formulário.
   * @return JsonResponse Resposta JSON do serviço.
   */
  public function getById(int $formId) :JsonResponse{
    $register = $this->obFormService->getUserFormById($formId, auth()->id());

    $response = new BusinessResponse(200, $register);
    return $response->build();
  }

  /**
   * Busca formulário associado a um anúncio por ID.
   * @param int $announcementId ID do anúncio.
   * @return JsonResponse Resposta JSON do serviço.
   */
  public function getByAnnouncement(int $announcementId) :JsonResponse {
    $register = $this->obFormService->getFormByAnnouncement($announcementId);

    unset($register->user);
    $response = new BusinessResponse(200, $register);
    return $response->build();
  }

  /**
   * Cria um novo formulário para o usuário autenticado
   * @param  UserFormRequest $request Objeto de requisição contendo os dados do formulário a ser criado
   * @return JsonResponse             Resposta JSON do serviço.
   */
  public function create(UserFormRequest $request): JsonResponse {
    $obFormRequest = array_merge($request->validated(), ['userId' => auth()->id()]);
    $registers     = $this->obFormService->create($obFormRequest);
    return new BusinessResponse(200, $registers)->build();
  }

  /**
   * Atualiza formulários de anúncios.
   * @param  int $formId              ID do formulário a ser atualizado.
   * @param  UserFormRequest $request Objeto de requisição contendo os dados do formulário a ser atualizado
   * @return JsonResponse             Resposta JSON do serviço.
   */
  public function update(int $formId, UserFormRequest $request) :JsonResponse {
    $obFormRequest = array_merge($request->validated(), ['userId' => auth()->id()]);
    $registers     = $this->obFormService->edit($formId, $obFormRequest);
    return new BusinessResponse(200, $registers)->build();
  }

  /**
   * Exclui formulários de anúncios.
   * @param  int $formId  ID do formulário a ser excluído.
   * @return JsonResponse Resposta JSON do serviço.
   */
  public function delete(int $formId) :JsonResponse {
    $this->obFormService->remove($formId);
    return new BusinessResponse(200, 'Formulário excluído com sucesso')->build();
  }
}
