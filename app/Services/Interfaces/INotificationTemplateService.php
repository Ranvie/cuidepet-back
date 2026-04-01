<?php

namespace App\Services\Interfaces;

use App\DTO\NotificationTemplate\NotificationTemplateDTO;

/**
 * Interface de serviço para gerenciar templates de notificação.
 */
interface INotificationTemplateService {

  /**
   * Lista todos os templates de notificação.
   * @param  int $limit Número de templates por página.
   * @param  int $page  Número da página.
   * @return array
   */
  public function getList(int $limit, int $page) :array;

  /**
   * Obtém um template de notificação por ID.
   * @param  int $templateId
   * @return NotificationTemplateDTO
   */
  public function getNotificationTemplateById(int $templateId) :NotificationTemplateDTO;

  /**
   * Obtém um template de notificação por tipo.
   * @param  string $type
   * @return NotificationTemplateDTO
   */
  public function getNotificationTemplateByType(string $type) :NotificationTemplateDTO;

}