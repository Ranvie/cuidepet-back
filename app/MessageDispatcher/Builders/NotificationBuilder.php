<?php

namespace App\MessageDispatcher\Builders;

use App\DTO\Notification\NotificationDataDTO;
use App\DTO\Notification\NotificationDataDTOAction;
use App\Exceptions\BusinessException;
use App\Http\Enums\NotificationTypes;
use App\MessageDispatcher\Contracts\Builder;
use App\MessageDispatcher\Contracts\Message;
use App\MessageDispatcher\Messages\NotificationMessage;
use App\MessageDispatcher\Senders\InAppSender;
use App\Utils\RequiredFieldsValidator;

/**
 * Builder para criar mensagens de notificação in-app
 */
class NotificationBuilder implements Builder {

  /**
   * IDs dos usuários destinatários da notificação
   * @var array
   */
  private array $userIds = [];

  /**
   * Tipo da notificação, utilizado para determinar o conteúdo e comportamento da notificação in-app
   * @var NotificationTypes
   */
  private NotificationTypes $type;

  /**
   * Dados adicionais para a notificação
   * @var array|null
   */
  private ?array $data = null;

  /**
   * Construtor do builder, que recebe os dados necessários para configurar a mensagem de notificação in-app
   * @param ?array            $userIds IDs dos usuários destinatários da notificação, que podem ser utilizados para enviar a notificação apenas para um grupo específico de usuários
   * @param NotificationTypes $type    Tipo da notificação, utilizado para determinar o conteúdo e comportamento da notificação in-app
   * @param array|null        $data    Dados adicionais para a notificação, que podem ser utilizados para personalizar o conteúdo da notificação ou fornecer informações adicionais para o processamento da mensagem
   */
  public function __construct(?array $userIds, NotificationTypes $type, ?array $data = null) {
    $this->userIds = $userIds;
    $this->type    = $type;
    $this->data    = $data;
  }

  /**
   * Constrói a mensagem de notificação in-app com as configurações definidas no builder
   * @return Message Retorna uma instância de NotificationMessage contendo os dados configurados
   * @throws BusinessException Lança uma exceção do tipo BusinessException caso os dados fornecidos para construir a mensagem de notificação in-app sejam inválidos ou insuficientes para criar uma mensagem válida
   */
  public function build() :NotificationMessage {
    if (empty($this->userIds))
      throw new BusinessException('Nenhum usuário destinatário fornecido para a notificação.', 422);

    return new NotificationMessage($this->userIds, $this->getNotificationDataByType($this->type, $this->data)); 
  }

  /**
   * Obtém o sender responsável por enviar a mensagem de notificação in-app, que é o InAppSender
   * @return InAppSender Retorna uma instância do sender InAppSender, que é responsável por enviar mensagens para o canal in-app
   */
  public function getSender() :InAppSender {
    return app(InAppSender::class);
  }

  /**
   * Gera os dados adicionais para a notificação com base no tipo e nos dados fornecidos.
   * @param  NotificationTypes $type Tipo da notificação.
   * @param  array|null        $data Dados adicionais para a notificação.
   * @return NotificationDataDTO     Dados da notificação.
   */
  private function getNotificationDataByType(NotificationTypes $type, ?array $data = null) :NotificationDataDTO {
    return match ($type) {
      NotificationTypes::WELCOME                       => $this->getNotificationData(NotificationTypes::WELCOME,                       'announcement.create', [],    []),
      NotificationTypes::ANNOUNCEMENT_ALERT            => $this->getNotificationData(NotificationTypes::ANNOUNCEMENT_ALERT,            'announcement.view',   $data, ['announcementId', 'type']),
      NotificationTypes::NEW_RESPONSE                  => $this->getNotificationData(NotificationTypes::NEW_RESPONSE,                  'announcement.view',   $data, ['announcementId', 'petName']),
      NotificationTypes::ANNOUNCEMENT_UPDATE           => $this->getNotificationData(NotificationTypes::ANNOUNCEMENT_UPDATE,           'announcement.view',   $data, ['announcementId']),
      NotificationTypes::PET_FOUND                     => $this->getNotificationData(NotificationTypes::PET_FOUND,                     'announcement.view',   $data, ['announcementId', 'petName']),
      NotificationTypes::PET_ADOPTED                   => $this->getNotificationData(NotificationTypes::PET_ADOPTED,                   'announcement.view',   $data, ['announcementId', 'petName']),
      NotificationTypes::ANNOUNCEMENT_PAUSED           => $this->getNotificationData(NotificationTypes::ANNOUNCEMENT_PAUSED,           'none',                $data, ['petName']),
      NotificationTypes::FAVORITED_ANNOUNCEMENT_PAUSED => $this->getNotificationData(NotificationTypes::FAVORITED_ANNOUNCEMENT_PAUSED, 'none',                $data, ['petName']),
      NotificationTypes::FORM_PAUSED                   => $this->getNotificationData(NotificationTypes::FORM_PAUSED,                   'none',                $data, ['title'])
    };
  }

  /**
   * Gera os dados adicionais para a notificação com base no tipo e nos dados fornecidos.
   * @param  NotificationTypes $type           Tipo da notificação.
   * @param  string           $action         Ação da notificação.
   * @param  array|null       $params         Parâmetros da notificação.
   * @param  array|null       $requiredParams Parâmetros obrigatórios da notificação.
   * @return NotificationDataDTO              Dados da notificação.
   */
  private function getNotificationData(NotificationTypes $type, string $action, ?array $params, ?array $requiredParams) :NotificationDataDTO {
    $this->validateRequiredParams($type, $params, $requiredParams);
    
    // Remove parâmetros extras
    $params = array_intersect_key($params ?? [], array_flip($requiredParams ?? []));

    $actionDTO             = new NotificationDataDTOAction();
    $actionDTO->name       = $action;
    $actionDTO->parameters = $params;

    $notificationDataDTO         = new NotificationDataDTO();
    $notificationDataDTO->type   = $type;
    $notificationDataDTO->action = $actionDTO;

    return $notificationDataDTO;
  }

  /**
   * Valida os parâmetros obrigatórios para um tipo de notificação específico, garantindo que todos os parâmetros necessários estejam presentes e tenham valores válidos antes de construir a mensagem de notificação in-app
   * @param  NotificationTypes $type           Tipo da notificação, utilizado para fornecer contexto na mensagem de erro caso os parâmetros obrigatórios estejam ausentes ou inválidos
   * @param  array|null        $params         Parâmetros fornecidos para a notificação, que serão validados em relação aos parâmetros obrigatórios definidos para o tipo de notificação
   * @param  array|null        $requiredParams Parâmetros obrigatórios para o tipo de notificação, que devem estar presentes e ter valores válidos para que a mensagem de notificação in-app seja construída corretamente
   * @return void
   * @throws BusinessException Lança uma exceção do tipo BusinessException caso os parâmetros obrigatórios estejam ausentes ou inválidos
   */
  private function validateRequiredParams(NotificationTypes $type, ?array $params, ?array $requiredParams) :void {
    if (!RequiredFieldsValidator::validate($params, $requiredParams))
      throw new BusinessException("Parâmetros obrigatórios ausentes para o tipo de notificação '{$type->value}': " . implode(', ', $requiredParams));
  }
}
