<?php

namespace App\Http\Controllers;

use App\Classes\Ordenation;
use App\Classes\Validators\PublicAnnouncementFilterValidator;
use App\Http\Requests\ListingRequest;
use App\Http\Response\BusinessResponse;
use App\Services\PublicAnnouncementService;
use Illuminate\Http\JsonResponse;

/**
 * Controlador responsável por lidar com as requisições relacionadas aos anúncios públicos.
 * Fornece métodos para listar e obter detalhes de anúncios públicos.
 */
class PublicAnnouncementController {

  /**
   * Método construtor da classe
   * @param PublicAnnouncementService $obPublicAnnouncementService
   */
  public function __construct(
    private PublicAnnouncementService $obPublicAnnouncementService,
  ) {}

  /**
   * Lista os anúncios públicos com base no tipo de anúncio
   * @param  ListingRequest $request Objeto de requisição contendo os parâmetros de paginação e filtros
   * @return JsonResponse            Resposta JSON contendo a lista de anúncios públicos
   */
  public function list(ListingRequest $request) :JsonResponse {
    $validated          = $request->validated();
    [$filters, $orders] = (new PublicAnnouncementFilterValidator())->build($request);

    $registers = $this->obPublicAnnouncementService->getList($validated['limit'], $validated['page'], $filters, $orders);

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }

  /**
   * Obtém os detalhes de um anúncio público específico com base no ID do anúncio
   * @param  int $announcementId O ID do anúncio público a ser obtido
   * @return JsonResponse        Resposta JSON contendo os detalhes do anúncio público
   */
  public function get(int $announcementId) :JsonResponse {
    $obAnnouncementDTO = $this->obPublicAnnouncementService->getById($announcementId);

    $response = new BusinessResponse(200, $obAnnouncementDTO);
    return $response->build();
  }
}
