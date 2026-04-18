<?php

namespace App\Services;

use App\DTO\Notification\NotificationDTO;
use App\Models\NotificationModel;
use App\Services\Interfaces\INotificationService;
use \App\Exceptions\BusinessException;

/**
 * Serviço responsável por gerenciar as operações relacionadas às notificações.
 */
class NotificationService implements INotificationService {

  /**
   * Método Construtor
   * @param NotificationModel $notificationModel
   */
  public function __construct(
    private NotificationModel $notificationModel,
    private NotificationTemplateService $notificationTemplateService
  ) {}

  /**
   * Lista os registros com paginação.
   * @param  int $limit        Número de registros por página.
   * @param  int $page         Número da página.
   * @param  array $filters    Filtros para a consulta.
   * @param  array $orders     Ordenações para a consulta.
   * @return NotificationDTO[] Lista de registros paginada.
   */
  public function getList(int $limit, int $page, array $filters = [], array $orders = []) :array {
    $notifications = $this->notificationModel->list($limit, $page, relations: ['notificationTemplate'], filters: $filters, orders: $orders);
    return $this->processTemplates($notifications);
  }

  /**
   * Processa os templates das notificações, substituindo os placeholders pelos valores correspondentes.
   * @param  array $notifications Lista de notificações a serem processadas.
   * @return array
   */
  private function processTemplates(array $notifications) :array {
    foreach ($notifications as $notification) {
      $params = $notification->data['action']['params'] ?? [];
      foreach($params as $param) {
        $notification->notificationTemplate->content = str_replace("{$param}", $params[$param], $notification->notificationTemplate->content);
      }
    }
    return $notifications;
  }

  /**
   * Obtém um registro por ID.
   * @param  int   $id        ID do registro.
   * @param  array $relations Relacionamentos a serem carregados.
   * @return NotificationDTO Objeto com os detalhes do registro.
   */
  public function getById(int $id, array $relations = []) :NotificationDTO {
    $notification = $this->notificationModel->getById($id, $relations, true);
    $this->validateIfExists($notification);
    return $notification;
  }

  /**
   * Cria um novo registro.
   * @param  array  $data Dados do registro a ser criado.
   * @return object       Objeto com os detalhes do registro criado.
   */
  public function create(array $data) :object {
    $type = $data['type'] instanceof NotificationType 
      ? $data['type'] 
      : NotificationType::tryFrom($data['type']);
    
    $notificationTemplate = $this->notificationTemplateService->getNotificationTemplateByType($type);
    $this->notificationTemplateService->validateIfExists($notificationTemplate);

    $data['notification_template_id'] = $notificationTemplate->id;
    return $this->notificationModel->create($data);
  }

  /**
   * Edita um registro existente.
   * @param  int    $id      ID do registro a ser editado.
   * @param  array  $data    Dados atualizados do registro.
   * @return NotificationDTO Objeto com os detalhes do registro atualizado.
   */
  public function edit(int $id, array $data) :NotificationDTO {
    $notification = $this->notificationModel->getById($id, [], true);
    $this->validateIfExists($notification);

    $notification = $this->notificationModel->edit($id, $data);
    return $notification;
  }

  /**
   * Remove um registro.
   * @param  ?int $id ID do registro a ser removido.
   * @return bool     Indica se a remoção foi bem-sucedida.
   */
  public function remove(?int $id = null) :bool {
    $notification = $this->notificationModel->getById($id);
    $this->validateIfExists($notification);

    return $this->notificationModel->remove($id);
  }

  /**
   * Marca uma notificação como lida.
   * @param  int $notificationId ID da notificação a ser marcada como lida.
   * @return void
   */
  public function readNotification(int $notificationId) :void {
    $notification = $this->notificationModel->getById($notificationId, ['notificationTemplate']);
    $this->validateIfExists($notification);

    $this->notificationModel->edit($notificationId, ['viewed' => true]);
  }

  /**
   * Valida se a notificação existe.
   * @param  NotificationDTO|NotificationModel|null $notification Notificação a ser validada.
   * @return void
   * @throws BusinessException Se a notificação não for encontrada.
   */
  public function validateIfExists(NotificationDTO|NotificationModel|null $notification) :void {
    if (!$notification instanceof NotificationDTO)
      throw new BusinessException("Notificação não encontrada.", 404);
  }

  /**
   * Envia uma notificação para um usuário específico.
   * @param  NotificationType $type   Tipo da notificação a ser enviada.
   * @param  int              $userId ID do usuário destinatário da notificação.
   * @param  array|null       $data    Dados adicionais a serem incluídos na notificação (opcional).
   * @return void
   */
  public function sendNotification(NotificationType $type, int $userId, ?array $data = null) :void {
    $notificationTemplate = $this->notificationTemplateService->getNotificationTemplateByType($type);
    $this->notificationTemplateService->validateIfExists($notificationTemplate);

    $this->create([
      'type'    => $type->value,
      'user_id' => $userId,
      'data'    => $this->getNotificationDataByType($type, $data)
    ]);
  }

  /**
   * Gera os dados adicionais para a notificação com base no tipo e nos dados fornecidos.
   * @param  NotificationType $type Tipo da notificação.
   * @param  array|null       $data Dados adicionais para a notificação.
   * @return array                  Dados da notificação.
   */
  private function getNotificationDataByType(NotificationType $type, ?array $data = null) :array {
    return match ($type) {
      NotificationType::WELCOME             => $this->getNotificationData(NotificationType::WELCOME,             'announcement.create',    [], []),
      NotificationType::ANNOUNCEMENT_ALERT  => $this->getNotificationData(NotificationType::ANNOUNCEMENT_ALERT,  'announcement.view',   $data, ['announcementId', 'type']),
      NotificationType::NEW_RESPONSE        => $this->getNotificationData(NotificationType::NEW_RESPONSE,        'announcement.view',   $data, ['announcementId', 'petName']),
      NotificationType::ANNOUNCEMENT_UPDATE => $this->getNotificationData(NotificationType::ANNOUNCEMENT_UPDATE, 'announcement.view',   $data, ['announcementId']),
      NotificationType::PET_FOUND           => $this->getNotificationData(NotificationType::PET_FOUND,           'announcement.view',   $data, ['announcementId', 'petName']),
      NotificationType::PET_ADOPTED         => $this->getNotificationData(NotificationType::PET_ADOPTED,         'announcement.view',   $data, ['announcementId', 'petName']),
      NotificationType::ANNOUNCEMENT_PAUSED => $this->getNotificationData(NotificationType::ANNOUNCEMENT_PAUSED, 'none',                $data, [])
    };
  }

  /**
   * Gera os dados adicionais para a notificação com base no tipo e nos dados fornecidos.
   * @param  NotificationType $type           Tipo da notificação.
   * @param  string           $action         Ação da notificação.
   * @param  array|null       $params         Parâmetros da notificação.
   * @param  array|null       $requiredParams Parâmetros obrigatórios da notificação.
   * @return array                            Dados da notificação.
   */
  private function getNotificationData(NotificationType $type, string $action, ?array $params, ?array $requiredParams) :array {
    $missingParams = array_diff($requiredParams ?? [], array_keys($params ?? []));
    if (!empty($missingParams))
      throw new BusinessException("Parâmetros obrigatórios ausentes para o tipo de notificação '{$type->value}': " . implode(', ', $missingParams));

    // Filtra os parâmetros para incluir apenas os obrigatórios
    $params = array_intersect_key($params ?? [], array_flip($requiredParams ?? []));

    return [
      'type'   => $type,
      'action' => [
        'type'   => $action,
        'params' => $params
      ]
    ];
  }
}
