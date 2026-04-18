<?php

namespace App\Http\Controllers;

use App\Classes\Filter;
use App\Classes\Ordenation;
use App\Http\Requests\ListingRequest;
use App\Http\Response\BusinessResponse;
use App\Services\NotificationService;
use \Illuminate\Http\JsonResponse;

/**
 * Controlador responsável por gerenciar as operações relacionadas às notificações.
 */
class NotificationController {

  /**
   * Método Construtor
   * @param NotificationService $notificationService
   */
  public function __construct(
    private NotificationService $notificationService
  ) {}

  /** 
   * Método responsável por listar as notificações do usuário autenticado.
   * @param ListingRequest $request Requisição contendo os parâmetros de paginação.
   * @return JsonResponse
   */
  public function list(ListingRequest $request) :JsonResponse {
    $validated     = $request->validated();
    $filters       = [new Filter('user_id', '=', auth()->id())];
    $orders        = [new Ordenation('created_at', 'desc')];
    $notifications = $this->notificationService->getList($validated['limit'], $validated['page'], filters: $filters, orders: $orders);
    
    $response = new BusinessResponse(200, $notifications);
    return $response->build();
  }

  /**
   * Remove uma notificação do usuário autenticado.
   * @param  int $notificationId ID da notificação a ser removida.
   * @return JsonResponse
   */
  public function delete(int $notificationId) :JsonResponse {
    $this->notificationService->remove($notificationId);

    $response = new BusinessResponse(200, "Notificação deletada com sucesso.");
    return $response->build();
  }

  /**
   * Marca uma notificação como lida.
   * @param  int $notificationId ID da notificação a ser marcada como lida.
   * @return JsonResponse
   */
  public function readNotification(int $notificationId) :JsonResponse {
    $this->notificationService->readNotification($notificationId);

    $response = new BusinessResponse(200, "Notificação marcada como lida com sucesso.");
    return $response->build();
  }
}
