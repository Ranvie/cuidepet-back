<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingRequest;
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
   * @param  ListingRequest $request Requisição contendo os parâmetros de paginação
   * @return JsonResponse            Resposta JSON contendo a lista de espécies
   */
  public function list(ListingRequest $request) :JsonResponse {
    $validated = $request->validated();
    $registers = $this->specieService->getList($validated['limit'], $validated['page']);

    $response = new BusinessResponse(200, $registers);
    return $response->build();
  }
}
