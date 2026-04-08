<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Http\Response\BusinessResponse;
use App\Services\AnnouncementService;
use Illuminate\Http\JsonResponse;

class AnnouncementController {

  /**
   * Construtor do controlador de anúncios.
   * @param AnnouncementService $obAnnouncementService Serviço de anúncios.
   */
  public function __construct(
    private AnnouncementService $obAnnouncementService
  ) {}

  /**
   * Lista os anúncios do usuário autenticado.
   * @return JsonResponse Resposta JSON com a lista de anúncios.
   */
  public function list() :JsonResponse {
    $registers = $this->obAnnouncementService->getListByUser(10, 1, auth()->id());

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }

  /**
   * Obtém um anúncio específico.
   * @param  int $announcementId ID do anúncio.
   * @return JsonResponse        Resposta JSON com os detalhes do anúncio.
   */
  public function get(int $announcementId) :JsonResponse {
    $obAnnouncementDTO = $this->obAnnouncementService->getUserAnnouncement($announcementId, auth()->id());

    $response = new BusinessResponse(200, $obAnnouncementDTO);
    return $response->build();
  }

  /**
   * Cria um novo anúncio.
   * @param  AnnouncementRequest $request Requisição contendo os dados do anúncio.
   * @return JsonResponse                 Resposta JSON com os detalhes do anúncio criado.
   */
  public function create(AnnouncementRequest $request) :JsonResponse {
    $requestData           = $request->validated();
    $requestData['userId'] = auth()->id();
    $obAnnouncementDTO     = $this->obAnnouncementService->create($requestData);

    $response = new BusinessResponse(200, $obAnnouncementDTO);
    return $response->build();
  }

  /**
   * Atualiza um anúncio existente.
   * @param  int                 $announcementId ID do anúncio a ser atualizado.
   * @param  AnnouncementRequest $request        Requisição contendo os dados atualizados do anúncio.
   * @return JsonResponse                        Resposta JSON com os detalhes do anúncio atualizado.
   */
  public function update(int $announcementId, AnnouncementRequest $request) :JsonResponse {
    $requestData           = $request->validated();
    $requestData['userId'] = auth()->id();
    $obAnnouncementDTO     = $this->obAnnouncementService->edit($announcementId, $requestData);

    $response = new BusinessResponse(200, $obAnnouncementDTO);
    return $response->build();
  }

  /**
   * Remove um anúncio.
   * @param  int $announcementId ID do anúncio a ser removido.
   * @return JsonResponse        Resposta JSON indicando o sucesso da operação.
   */
  public function delete(int $announcementId) :JsonResponse {
    $this->obAnnouncementService->remove($announcementId);
    $response = new BusinessResponse(200, "O anúncio $announcementId foi removido com sucesso.");
    return $response->build();
  }
}
