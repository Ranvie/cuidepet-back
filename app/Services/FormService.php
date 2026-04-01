<?php

namespace App\Services;

use App\DTO\Form\FormDTO;
use App\Models\FormModel;
use App\Services\Interfaces\IFormService;

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
    return $this->formModel->list($limit, $page, filters: ['user_id' => $userId]);
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
   * @param int $formId ID do formulário.
   * @param int $userId ID do usuário.
   * @return FormDTO Coleção de formulários do usuário.
   */
  public function getFormById(int $formId, int $userId) :FormDTO {
    $formResponse = $this->formModel->getById($formId, filters: ['user_id' => $userId]);

    $this->validateForm($formResponse);
    
    return $formResponse;
  }

  /**
   * Busca formulário associado a um anúncio por ID.
   * @param int $formId         ID do formulário.
   * @param int $announcementId ID do anúncio.
   * @return FormDTO Coleção de formulários do usuário.
   */
  public function getFormByAnnouncement(int $formId, int $announcementId) :FormDTO {
    $formResponse = $this->formModel->getById($formId, filters: ['announcement_id' => $announcementId]);
    
    $this->validateForm($formResponse);

    return $formResponse;
  }

  /**
   * Cadastra formulários de anúncios.
   * @param array $data Dados do formulário a ser cadastrado.
   * @return FormDTO DTO do formulário cadastrado.
   */
  public function create(array $data) :FormDTO {
    return $this->formModel->create($data);
  }

  /**
   * Atualiza formulários de anúncios.
   * @param int $formId ID do formulário a ser atualizado.
   * @param int $userId ID do usuário associado.
   * @param array $data Dados do formulário a ser atualizado.
   * @return FormDTO DTO do formulário atualizado.
   */
  public function edit(int $formId, int $userId, array $data) :FormDTO {
    $obFormDTO = $this->formModel->edit($formId, $data, filters: ['user_id', $userId], parse: false);

    if(!$obFormDTO instanceof FormDTO)
      throw new \Exception("Ocorreu um erro ao atualizar o formulário.");

    return $obFormDTO;
  }

  /**
   * Exclui formulários de anúncios.
   * @param int $formId ID do formulário a ser excluído.
   * @param int $userId ID do usuário dono do formulário.
   * @return bool True se a exclusão foi bem-sucedida, false caso contrário.
   */
  public function remove(int $formId, int $userId) :bool {
    return $this->formModel->delete($formId, $userId);
  }

  /**
   * Método responsável por validar um formulário
   * @param FormDTO|null $formDTO O formulário a ser validado.
   * @return void
   */
  private function validateForm(?FormDTO $formDTO) :void {
    if (!$formDTO instanceof FormDTO)
      throw new \Exception("O formulário solicitado não foi encontrado.");
  }
}
