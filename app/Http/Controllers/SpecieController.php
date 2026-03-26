<?php

namespace App\Http\Controllers;

use App\Http\Response\BusinessResponse;
use App\Services\SpecieService;
use Illuminate\Http\JsonResponse;

class SpecieController {

  /**
   * Método Construtor
   * @param SpecieService $specieService Serviço responsável pelas operações relacionadas a espécies
   */
  public function __construct(
    private SpecieService $specieService,
  ) {}

  /**
   * Lista as espécies do banco de dados
   * @return JsonResponse Resposta JSON contendo a lista de espécies
   */
  public function list(): JsonResponse {
    $registers = $this->specieService->getList(50, 1);

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }
}
