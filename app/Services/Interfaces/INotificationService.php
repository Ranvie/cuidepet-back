<?php

namespace App\Services\Interfaces;

interface INotificationService extends IService {

  /**
   * Marca uma notificação como lida.
   * @param  int $userId         ID do usuário.
   * @param  int $notificationId ID da notificação.
   * @return void
   */
  public function readNotification(int $userId, int $notificationId) :void;

}
