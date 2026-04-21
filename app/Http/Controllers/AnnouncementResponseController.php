<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormResponseRequest;
use App\Http\Requests\ListingRequest;
use App\Http\Response\BusinessResponse;
use App\Services\AnnouncementResponseService;
use Illuminate\Http\JsonResponse;

class AnnouncementResponseController {
  
  /**
   * Método Construtor
   * @param AnnouncementResponseService $obAnnouncementResponseService Instância do serviço de resposta de anúncio para manipular as respostas dos anúncios.
   */
  public function __construct(
    private AnnouncementResponseService $obAnnouncementResponseService
  ) {}

  /**
   * Listagem de resposta do anúncio
   * @param  int            $announcementId ID do anúncio cujo as respostas devem ser listadas
   * @param  ListingRequest $request        Request de listagem para parâmetros de paginação
   * @return JsonResponse                   Resposta JSON do serviço.
   */
  public function list(int $announcementId, ListingRequest $request) :JsonResponse {
    $validated = $request->validated();
    $registers = $this->obAnnouncementResponseService->listAnnouncementResponses($validated['limit'], $validated['page'], $announcementId);

    return new BusinessResponse(200, $registers)->build();
  }

  /**
   * Busca de resposta de um anúncio
   * @param  int $announcementId ID do anúncio cujo a resposta deve ser consultada
   * @param  int $responseId     ID da resposta a ser consultada
   * @return JsonResponse        Resposta JSON do serviço.
   */
  public function get(int $announcementId, int $responseId) :JsonResponse {
    $register = $this->obAnnouncementResponseService->getAnnouncementResponseById($announcementId, $responseId);

    return new BusinessResponse(200, $register)->build();
  }

  /**
   * Verifica se o usuário já respondeu determinado anúncio anteriormente
   * @param  int $announcementId ID do anúncio respondido
   * @return JsonResponse        Resposta JSON do serviço.
   */
  public function validateResponse(int $announcementId) :JsonResponse {
    $register = $this->obAnnouncementResponseService->checkIfUserResponded($announcementId, auth()->id());

    return new BusinessResponse(200, $register)->build();
  }

  /**
   * Cadastra uma resposta de formulário para um anúncio
   * @param  int                 $announcementId ID do anúncio para o qual a resposta está sendo cadastrada
   * @param  FormResponseRequest $request        Dados de requisição do formulário
   * @return JsonResponse                        Resposta JSON do serviço.
   */
  public function create(int $announcementId, FormResponseRequest $request) :JsonResponse {
    $responseData = array_merge($request->validated(), ['user_id' => auth()->id(), 'announcement_id' => $announcementId]);
    $register     = $this->obAnnouncementResponseService->create($responseData);

    return new BusinessResponse(200, $register)->build();
  }

  /**
   * Exclui uma resposta de um anúncio
   * @param  int $announcementId ID do anúncio cujo a resposta deve ser excluída
   * @param  int $responseId     ID da resposta que deve ser excluída
   * @return JsonResponse
   */
  public function delete(int $announcementId, int $responseId) :JsonResponse {
    $this->obAnnouncementResponseService->remove($announcementId, $responseId);
    return new BusinessResponse(200, 'Resposta excluída com sucesso')->build();
  }
}
