<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Http\Response\BusinessResponse;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController {

  /**
   * Método Construtor
   * @param ReportService $obReportService Serviço para processamento de denúncias
   */
  public function __construct(
    private ReportService $obReportService
  ){}

  /**
   * Responsável por cadastrar a denúncia de um anúncio
   * @param  ReportRequest $request Request contendo conteúdo para cadastro da denúncia
   * @return JsonResponse           Resposta JSON da operação
   */
  public function create(ReportRequest $request) :JsonResponse {
    $data = array_merge($request->validated(), ['userId' => auth()->id()]);
    $this->obReportService->create($data);

    return new BusinessResponse(200, 'Denúncia realizada com sucesso!')->build();
  }

}
