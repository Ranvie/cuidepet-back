<?php

namespace App\Http\Controllers;

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
   * @param int $limit Número de notificações por página.
   * @param int $page  Número da página atual.
   * @return JsonResponse
   */
  public function list(int $limit = 10, int $page = 1) {
    $notifications = $this->notificationService->getList($limit, $page);
    
    $response = new BusinessResponse(200, $notifications);
    return $response->build();
  }

  /**
   * Método responsável por obter os detalhes de uma notificação específica.
   * @param  int $notificationId ID da notificação.
   * @return JsonResponse
   */
  public function delete(int $notificationId) {
    $this->notificationService->remove($notificationId);

    $response = new BusinessResponse(200, "Notificação deletada com sucesso.");
    return $response->build();
  }

  /**
   * Método responsável por marcar uma notificação como lida.
   * @param  int $notificationId ID da notificação a ser marcada como lida.
   * @return JsonResponse
   */
  public function readNotification(int $notificationId) {
    $userId = auth()->id();
    $this->notificationService->readNotification($notificationId, $userId);

    $response = new BusinessResponse(200, "Notificação marcada como lida com sucesso.");
    return $response->build();
  }
}
