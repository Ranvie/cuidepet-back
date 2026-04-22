<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Requests\NewsletterRequest;
use App\Http\Response\BusinessResponse;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador responsável por gerenciar as operações relacionadas à newsletter, incluindo
 * inscrição, cancelamento de inscrição e confirmação de inscrição.
 */
class NewsletterController {

  /**
   * Método Construtor
   * @param NewsletterService $newsletterService
   */
  public function __construct(
    private NewsletterService $newsletterService
  ){}
  
  /**
   * Inscreve um usuário na newsletter.
   * @param  NewsletterRequest $request Requisição contendo os dados necessários para inscrição na newsletter
   * @return JsonResponse
   */
  public function subscribe(NewsletterRequest $request) :JsonResponse {
    $validated = $request->validated();
    $this->newsletterService->sendNewsletterMailConfirmation($validated['email'], $validated['zipcode']);

    return new BusinessResponse(200, 'Inscrição realizada com sucesso.')->build();
  }

  /**
   * Confirma a inscrição de um usuário na newsletter.
   * @param  Request $request  Requisição contendo o token de confirmação da inscrição
   * @return JsonResponse
   * @throws BusinessException Se o token for inválido ou expirado, ou se ocorrer algum erro durante a confirmação da inscrição
   */
  public function confirmSubscription(Request $request) :JsonResponse {
    $token = $request->query('token');
    $this->newsletterService->confirmNewsletterSubscription($token);

    return new BusinessResponse(200, 'Newsletter confirmada com sucesso.')->build();
  }

  /**
   * Cancela a inscrição de um usuário na newsletter.
   * @param  Request $request Requisição contendo o token de cancelamento da inscrição
   * @return JsonResponse
   */
  public function unsubscribe(Request $request) :JsonResponse {
    $token = $request->query('token');
    $this->newsletterService->unsubscribeByToken($token);

    return new BusinessResponse(200, 'Inscrição cancelada com sucesso.')->build();
  }

}