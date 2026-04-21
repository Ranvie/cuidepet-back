<?php

namespace App\MessageDispatcher\Notifications;

use App\Http\Enums\NotificationTypes;
use App\MessageDispatcher\Builders\EmailBuilder;
use App\MessageDispatcher\Builders\NotificationBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;

/**
 * Notificação disparada quando um novo anúncio é criado na região de um inscrito.
 * Envia notificação in-app para usuários cadastrados e email para inscritos na newsletter.
 */
class AnnouncementAlertNotification {

  /**
   * Método Construtor
   * @param array  $users            Lista de inscritos na newsletter a serem notificados (objetos com email, id e user relacionado)
   * @param int    $announcementId   ID do anúncio criado
   * @param string $announcementType Tipo do anúncio (ex: 'doação', 'perdido')
   */
  public function __construct(
    private array  $users,
    private int    $announcementId,
    private string $announcementType,
  ) {}

  /**
   * Dispara as notificações in-app e email para os destinatários resolvidos
   * @return void
   */
  public function send() :void {
    $userIds    = $this->resolvePushRecipients();
    $recipients = $this->resolveEmailRecipients();

    if (!empty($userIds)) {
      (new MessageDispatcher(new NotificationBuilder(
        $userIds,
        NotificationTypes::ANNOUNCEMENT_ALERT,
        ['type' => $this->getAnnouncementTypeForPush(), 'announcementId' => $this->announcementId]
      )))->dispatch();
    }

    if (!empty($recipients)) {
      (new MessageDispatcher(new EmailBuilder(
        $recipients,
        'Novo anúncio na sua área!',
        'mail.announcementAlert',
        [
          'type'            => $this->getAnnouncementTypeForEmail(),
          'announcementUrl' => config('app.frontend_url') . '/announcements/' . $this->announcementId,
        ]
      )))->dispatch();
    }
  }

  /**
   * Resolve os IDs dos usuários que devem receber notificação in-app
   * @return array Lista de IDs de usuários a serem notificados in-app
   */
  private function resolvePushRecipients() :array {
    $userIds = [];

    foreach ($this->users as $newsletter) {
      if (isset($newsletter->user->preference) && $newsletter->user->preference->receiveRegionAlarms ?? false)
        $userIds[] = $newsletter->user->id;
    }

    return $userIds;
  }

  /**
   * Mapeia o tipo do anúncio para um formato mais amigável para o email
   * @return string Tipo do anúncio formatado para email
   */
  private function getAnnouncementTypeForPush() :string {
    return match ($this->announcementType) {
      'doacao'  => 'em doação',
      'perdido' => 'perdido',
      default   => 'anúncio',
    };
  }

  /**
   * Resolve os destinatários de email com seus dados individuais (username e URL de desinscrição)
   * @return array Lista de destinatários de email no formato [['email' => string, 'vars' => array], ...]
   */
  private function resolveEmailRecipients() :array {
    $recipients = [];

    foreach ($this->users as $newsletter) {
      if (!isset($newsletter->user) || (isset($newsletter->user->preference) && $newsletter->user->preference->receiveAlarmsOnEmail ?? false)) {
        $recipients[] = [
          'email' => $newsletter->email,
          'vars'  => [
            'username'       => $newsletter->user->username ?? 'chegou novidade na área!',
            'unsubscribeUrl' => config('app.frontend_url') . '/unsubscribe/' . $newsletter->id,
          ],
        ];
      }
    }

    return $recipients;
  }

  private function getAnnouncementTypeForEmail() :string {
    return match ($this->announcementType) {
      'doacao'  => 'em doação',
      'perdido' => 'perdido',
      default   => '',
    };
  }
}
