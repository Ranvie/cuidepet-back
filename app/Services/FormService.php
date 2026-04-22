<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\Form\FormDTO;
use App\Exceptions\BusinessException;
use App\FormValidator\FormStructureValidator;
use App\Http\Enums\NotificationTypes;
use App\MessageDispatcher\Builders\NotificationBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;
use App\Models\FormModel;
use App\Services\Interfaces\IFormService;

/**
 * Serviço de gerenciamento de formulários.
 * Fornece métodos para criar, editar, listar e remover formulários, além de validações relacionadas a formulários.
 */
class FormService implements IFormService {

  /**
   * Método Construtor
   * @param FormModel $formModel
   */
  public function __construct(
    private FormModel $formModel
  ) {}

  /**
   * Lista paginada de formulários de um usuário específico.
   * @param  int $limit  Número de formulários por página.
   * @param  int $page   Número da página atual.
   * @param  int $userId ID do usuário.
   * @return FormDTO[]   Lista de formulários do usuário.
   */
  public function listFormsByUser(int $limit, int $page, int $userId) :array {
    return $this->formModel->list($limit, $page, filters: [new Filter('user_id', $userId)]);
  }

  /**
   * Lista todos os formulários de um usuário específico, sem paginação. (para select de formulário)
   * @param  int $userId ID do usuário.
   * @return array       Lista de todos os formulários do usuário.
   */
  public function listAllUserForms(int $userId) :array {
    return $this->formModel->listAll($userId);
  }

  /**
   * Busca formulário de um usuário por ID.
   * @param  int $formId ID do formulário.
   * @param  int $userId ID do usuário.
   * @return FormDTO     Coleção de formulários do usuário.
   */
  public function getUserFormById(int $formId, int $userId) :FormDTO {
    $formResponse = $this->formModel->getByQuery([new Filter('id', '=', $formId, 'AND'), new Filter('user_id', '=', $userId)]);

    $this->validateForm($formResponse);
    
    return $formResponse;
  }

  /**
   * Busca formulário associado a um anúncio por ID.
   * @param  int $announcementId ID do anúncio.
   * @return FormDTO Coleção de formulários do usuário.
   */
  public function getFormByAnnouncement(int $announcementId) :FormDTO {
    $formDTO = $this->formModel->getFormByAnnouncement($announcementId);
    
    $this->validateForm($formDTO);

    return $formDTO;
  }

  /**
   * Cadastra formulários de anúncios.
   * @param array $data Dados do formulário a ser cadastrado.
   * @return FormDTO DTO do formulário cadastrado.
   */
  public function create(array $data) :FormDTO {
    $requestPayload  = json_decode($data['payload'] ?? [], true) ?? [];
    $data['payload'] = new FormStructureValidator($requestPayload)->resolve();

    return $this->formModel->create($data);
  }

  /**
   * Atualiza formulários de anúncios.
   * @param  int   $formId     ID do formulário a ser atualizado.
   * @param  array $data       Dados do formulário a ser atualizado.
   * @return FormDTO           DTO do formulário atualizado.
   * @throws BusinessException Se o formulário solicitado não for encontrado ou se ocorrer um erro ao atualizar o formulário.
   */
  public function edit(int $formId, array $data) :FormDTO {
    $obFormDTO = $this->formModel->getById($formId);

    if(!$obFormDTO instanceof FormDTO)
      throw new BusinessException("Formulário solicitado não foi encontrado.", 404);

    if($obFormDTO->blocked)
      throw new BusinessException('Não é possível alterar um formulário pausado.', 403);

    if(isset($data['payload'])){
      $requestPayload  = json_decode($data['payload'] ?? "{}", true) ?? [];
      $data['payload'] = new FormStructureValidator($requestPayload)->resolve();
    }

    $obFormDTO = $this->formModel->edit($formId, $data);

    if(!$obFormDTO instanceof FormDTO)
      throw new BusinessException("Ocorreu um erro ao atualizar o formulário.", 500);

    if($obFormDTO->blocked)
      new MessageDispatcher(new NotificationBuilder([$obFormDTO->userId], NotificationTypes::FORM_PAUSED, ['title' => $obFormDTO->title]))->dispatch();

    return $obFormDTO;
  }

  /**
   * Exclui formulários de anúncios.
   * @param int $formId ID do formulário a ser excluído.
   * @return bool True se a exclusão foi bem-sucedida, false caso contrário.
   */
  public function remove(int $formId) :bool {
    return $this->formModel->remove($formId);
  }

  /**
   * Método responsável por validar um formulário
   * @param FormDTO|null $formDTO O formulário a ser validado.
   * @return void
   */
  private function validateForm(?FormDTO $formDTO) :void {
    if (!$formDTO instanceof FormDTO)
      throw new BusinessException("O formulário solicitado não foi encontrado.", 404);
  }
}
