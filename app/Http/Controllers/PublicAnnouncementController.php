<?php

namespace App\Http\Controllers;

use App\Http\Response\BusinessResponse;
use App\Services\PublicAnnouncementService;
use Illuminate\Http\JsonResponse;

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
   * @param  string $announcementType O tipo de anúncio a ser listado
   * @return JsonResponse             Resposta JSON contendo a lista de anúncios públicos
   */
  public function list(string $announcementType): JsonResponse {
    $registers = $this->obPublicAnnouncementService->getList(10, 1, $announcementType);

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }

  /**
   * Obtém os detalhes de um anúncio público específico com base no ID do anúncio
   * @param  int $announcementId O ID do anúncio público a ser obtido
   * @return JsonResponse        Resposta JSON contendo os detalhes do anúncio público
   */
  public function get(int $announcementId): JsonResponse {
    $obAnnouncementDTO = $this->obPublicAnnouncementService->getById($announcementId);

    $response = new BusinessResponse(200, $obAnnouncementDTO);
    return $response->build();
  }
}
