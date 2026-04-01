<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\Notification\NotificationDTO;
use App\Models\NotificationModel;
use App\Services\Interfaces\INotificationService;
use \App\Exceptions\BusinessException;

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
   * @return NotificationDTO[] Lista de registros paginada.
   */
  public function getList(int $limit, int $page) :array {
    return $this->notificationModel->list($limit, $page, relations: ['notificationTemplate']);
  }

  /**
   * Obtém um registro por ID.
   * @param  int   $id        ID do registro.
   * @param  array $relations Relacionamentos a serem carregados.
   * @return NotificationDTO Objeto com os detalhes do registro.
   */
  public function getById(int $id, array $relations) :NotificationDTO {
    $userId       = auth()->id(); //TODO: Tirar daqui
    $notification = $this->notificationModel->getByQuery([new Filter("id", "=", $id), new Filter("user_id", "=", $userId)], $relations);

    $this->validateIfExists($notification);
    return $notification;
  }

  //TODO: testar
  /**
   * Cria um novo registro.
   * @param  array  $data Dados do registro a ser criado.
   * @return object       Objeto com os detalhes do registro criado.
   */
  public function create(array $data) :object {
    $notificationTemplate = $this->notificationTemplateService->getNotificationTemplateByType($data['type']);
    $this->notificationTemplateService->validateIfExists($notificationTemplate);

    $data['user_id']                  = auth()->id(); //TODO: Tirar daqui
    $data['notification_template_id'] = $notificationTemplate->id;
    $register                         = $this->notificationModel->create($data);
    return $register;
  }

  //TODO: testar
  /**
   * Edita um registro existente.
   * @param  int    $id      ID do registro a ser editado.
   * @param  array  $data    Dados atualizados do registro.
   * @return NotificationDTO Objeto com os detalhes do registro atualizado.
   */
  public function edit(int $id, array $data) :NotificationDTO {
    $userId       = auth()->id(); //TODO: Tirar daqui
    $notification = $this->notificationModel->getByQuery([new Filter("id", "=", $id), new Filter("user_id", "=", $userId)]);
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
   * @param  int $userId         ID do usuário dono da notificação.
   * @param  int $notificationId ID da notificação a ser marcada como lida.
   * @return void
   */
  public function readNotification(int $notificationId, int $userId) :void {
    $notification = $this->notificationModel->getByQuery([new Filter("id", "=", $notificationId), new Filter("user_id", "=", $userId)], relations: ['notificationTemplate']);
    $this->validateIfExists($notification);

    $this->notificationModel->edit($notificationId, ['viewed' => true]);
  }

  /**
   * Valida se a notificação existe.
   * @param  NotificationDTO|NotificationModel|null $notification Notificação a ser validada.
   * @return void
   * @throws BusinessException                                    Se a notificação não for encontrada.
   */
  public function validateIfExists(NotificationDTO|NotificationModel|null $notification) :void {
    if (!$notification instanceof NotificationDTO)
      throw new BusinessException("Notificação não encontrada.", 404);
  }
}
