<?php

namespace App\Services;

use App\DTO\Notification\NotificationDataDTO;
use App\DTO\Notification\NotificationDTO;
use App\Http\Enums\NotificationTypes;
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
    foreach ($notifications['registers'] as $notification) {
      $params = json_decode($notification->data, true);
      foreach($params['action']['parameters'] ?? [] as $param => $value) {
        $notification->notificationTemplate->title   = str_replace("{{$param}}", $value, $notification->notificationTemplate->title);
        $notification->notificationTemplate->message = str_replace("{{$param}}", $value, $notification->notificationTemplate->message);
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
    $type = $data['type'] instanceof NotificationTypes 
      ? $data['type'] 
      : NotificationTypes::tryFrom($data['type']);
    
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
   * @param  int                 $userId ID do usuário destinatário da notificação.
   * @param  NotificationDataDTO $data   Dados adicionais a serem incluídos na notificação (opcional).
   * @return void
   */
  public function sendNotification(int $userId, NotificationDataDTO $data) :void {
    $notificationTemplate = $this->notificationTemplateService->getNotificationTemplateByType($data->type);
    $this->notificationTemplateService->validateIfExists($notificationTemplate);

    $arrayData = get_object_vars($data);

    $this->create([
      'type'    => $data->type->value,
      'user_id' => $userId,
      'data'    => json_encode($arrayData, JSON_UNESCAPED_UNICODE)
    ]);
  }
}
