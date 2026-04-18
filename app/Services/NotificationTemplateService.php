<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\NotificationTemplate\NotificationTemplateDTO;
use App\Models\NotificationTemplateModel;
use App\Services\Interfaces\INotificationTemplateService;
use \App\Exceptions\BusinessException;

/**
 * Enumeração dos tipos de notificação disponíveis.
 */
enum NotificationType: string {
  case WELCOME             = "welcome";
  case ANNOUNCEMENT_ALERT  = "announcement-alert";
  case NEW_RESPONSE        = "new-response";
  case ANNOUNCEMENT_UPDATE = "announcement-update";
  case PET_FOUND           = "pet-found";
  case PET_ADOPTED         = "pet-adopted";
  case ANNOUNCEMENT_PAUSED = "announcement-paused";
}

/**
 * Serviço responsável por gerenciar as operações relacionadas aos templates de notificação.
 */
class NotificationTemplateService implements INotificationTemplateService {

  /**
   * Método Construtor
   * @param NotificationTemplateModel $notificationTemplateModel
   */
  public function __construct(
    private NotificationTemplateModel $notificationTemplateModel
  ) {}

  /**
   * Obtém um template de notificação por ID.
   * @param  int $templateId
   * @return NotificationTemplateDTO
   */
  public function getNotificationTemplateById(int $templateId) :NotificationTemplateDTO {
    $templateResponse = $this->notificationTemplateModel->getById($templateId);

    $this->validateIfExists($templateResponse);
    return $templateResponse;

  }

  /**
   * Obtém um template de notificação por tipo.
   * @param NotificationType $type
   * @return NotificationTemplateDTO
   */
  public function getNotificationTemplateByType(NotificationType $type) :NotificationTemplateDTO {
    $templateResponse = $this->notificationTemplateModel->getByQuery([new Filter("type", "=", $type->value)]);

    $this->validateIfExists($templateResponse);
    return $templateResponse;
  }

  /**
   * Lista todos os templates de notificação.
   * @param  int $limit Número de templates por página.
   * @param  int $page  Número da página.
   * @return array
   */
  public function getList(int $limit, int $page) :array {
    return $this->notificationTemplateModel->list($limit, $page);
  }

  /**
   * Valida se o template de notificação existe.
   * @param object|null $template
   * @throws BusinessException Se o template não for encontrado.
   */
  public function validateIfExists(object|null $template) :void {
    if (!$template)
      throw new BusinessException("Template de notificação não encontrado.");
  }

}