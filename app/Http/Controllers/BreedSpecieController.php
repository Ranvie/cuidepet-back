<?php

namespace App\Http\Controllers;

use App\Http\Response\BusinessResponse;
use App\Services\BreedSpecieService;
use Illuminate\Http\JsonResponse;

class BreedSpecieController {

  /**
   * Método Construtor
   * @param BreedSpecieService $breedSpecieService Serviço responsável pelas operações relacionadas a raças e espécies
   */
  public function __construct(
    private BreedSpecieService $breedSpecieService,
  ) {}

  /**
   * Lista as raças e espécies do banco de dados
   * @return JsonResponse Resposta JSON contendo a lista de raças e espécies
   */
  public function list(): JsonResponse {
    $registers = $this->breedSpecieService->getList(50, 1);

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }
}
