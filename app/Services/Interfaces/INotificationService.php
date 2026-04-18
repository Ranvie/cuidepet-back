<?php

namespace App\Services\Interfaces;

interface INotificationService extends IService {

  /**
   * Marca uma notificação como lida.
   * @param  int $notificationId ID da notificação.
   * @return void
   */
  public function readNotification(int $notificationId) :void;

}
