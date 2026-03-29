<?php

namespace App\Http\Controllers;

use App\Http\Response\BusinessResponse;
use App\Services\AddressCacheService;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de endereços, responsável por lidar com as requisições relacionadas a endereços.
 */
class AddressController {

  public function __construct(
    private AddressCacheService $addressCacheService
  ) {}

  /**
   * Obtém um endereço específico pelo CEP.
   * @param  string $zipCode CEP do endereço.
   * @return JsonResponse    Resposta JSON com os detalhes do endereço.
   */
  public function get(string $zipCode) :JsonResponse {
    $obAddressDTO = $this->addressCacheService->getByZipCode($zipCode);

    $response = new BusinessResponse(200, $obAddressDTO);
    return $response->build();
  }

}