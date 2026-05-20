<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUsRequest;
use App\Http\Response\BusinessResponse;
use App\Services\ContactUsService;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para lidar com as requisições de contato.
 */
class ContactUsController {
  
  /**
   * Construtor do controlador de contato.
   * @param ContactUsService $contactUsService
   */
  public function __construct(
    private ContactUsService $contactUsService
  ) {}

  /**
   * Método para lidar com as requisições de contato.
   * @param  ContactUsRequest $request
   * @return JsonResponse
   */
  public function contactUs(ContactUsRequest $request) :JsonResponse {
    $data = $request->validated();
    $this->contactUsService->contactUs($data);

    return new BusinessResponse(200, 'Contato recebido com sucesso!')->build();
  }
}