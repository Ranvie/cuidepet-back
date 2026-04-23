<?php

namespace App\MessageDispatcher\Notifications;

use App\Http\Enums\NotificationTypes;
use App\MessageDispatcher\Builders\NotificationBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;
use App\Models\AnnouncementModel;

/**
 * Notificação disparada quando um anúncio é editado. Envia notificação in-app para usuários que favoritaram o anúncio.
 * O tipo da notificação varia conforme as mudanças feitas no anúncio (ex: se o status mudou para 'encontrado' ou 'adotado').
 */
class AnnouncementEditedNotification {

  /**
   * Método Construtor
   * @param array             $favoritedUserIds  Lista de IDs de usuários que favoritaram o anúncio editado
   * @param AnnouncementModel $announcementModel Instância do modelo do anúncio editado
   * @param array             $data              Dados enviados para edição do anúncio (usados para determinar tipo de notificação)
   */
  public function __construct(
    private array             $favoritedUserIds,
    private AnnouncementModel $announcementModel,
    private array             $data
  ) {}

  public function send(): void {
    $notificationType = NotificationTypes::ANNOUNCEMENT_UPDATE;

    if (isset($this->data['status']) && $this->data['status'] == true && $this->announcementModel->status == false) {
      $notificationType = $this->announcementModel->type === 'lost'
        ? NotificationTypes::PET_FOUND
        : NotificationTypes::PET_ADOPTED;
    }

    if (isset($this->data['blocked']) && $this->data['blocked'] == true && $this->announcementModel->blocked == false) {
      $notificationType = NotificationTypes::FAVORITED_ANNOUNCEMENT_PAUSED;

      new MessageDispatcher(new NotificationBuilder([$this->announcementModel->user_id], NotificationTypes::ANNOUNCEMENT_PAUSED, ['petName' => $this->announcementModel->animal->name]))->dispatch();
    }

    $obNotificationBuilder = match ($notificationType) {
      NotificationTypes::ANNOUNCEMENT_UPDATE                       => new NotificationBuilder($this->favoritedUserIds, $notificationType, ['announcementId' => $this->announcementModel->id]),
      NotificationTypes::PET_FOUND, NotificationTypes::PET_ADOPTED => new NotificationBuilder($this->favoritedUserIds, $notificationType, ['announcementId' => $this->announcementModel->id, 'petName' => $this->announcementModel->animal->name]),
      NotificationTypes::FAVORITED_ANNOUNCEMENT_PAUSED             => new NotificationBuilder($this->favoritedUserIds, $notificationType, ['petName'        => $this->announcementModel->animal->name])
    };

    new MessageDispatcher($obNotificationBuilder)->dispatch();
  }
}
