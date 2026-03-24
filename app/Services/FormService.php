<?php

namespace App\Services;

use App\DTO\Form\FormDTO;
use App\DTO\User\UserDTO;
use App\Models\FormModel;
use Illuminate\Support\Collection;

class FormService implements Interfaces\IFormService {

  /**
   * Método Construtor
   * @param FormModel $formModel
   */
  public function __construct(
    private FormModel $formModel
  ) {}

  /**
   * Retorna uma lista paginada de formulários
   * @param  int $limit Número de itens por página
   * @param  int $page  Número da página a ser retornada
   * @return array      Lista de formulários paginada
   */
  public function getList(int $limit, int $page) :array {
    // TODO: Implement getList() method.
    return [];
  }

  /**
   * Retorna uma lista de formulários criados por um usuário específico
   * @param  int $userId ID do usuário
   * @return Collection  Lista de formulários criados pelo usuário
   */
  public function listFormByUser(int $userId) :Collection {
    return $this->formModel->listFormByUser($userId);
  }

  /**
   * Retorna um formulário específico por ID
   * @param  int $id          ID do formulário
   * @param  array $relations Relacionamentos a serem carregados
   * @return UserDTO          Objeto com os detalhes do formulário
   */
  public function getById(int $id, array $relations = []) :UserDTO {
    return $this->formModel->getById($id, $relations);
  }

  /**
   * Retorna um formulário específico criado por um usuário
   * @param  int $userId ID do usuário
   * @param  int $formId ID do formulário
   * @return UserDTO     Objeto com os detalhes do formulário
   */
  public function getUserForm(int $userId, int $formId) :UserDTO {
    return $this->formModel->getUserForm($userId, $formId);
  }

  /**
   * Cria um novo formulário
   * @param  array $data Dados do formulário a ser criado
   * @return FormDTO     Objeto com os detalhes do formulário criado
   */
  public function create(array $data) :FormDTO {
    return $this->formModel->create($data);
  }

  /**
   * Edita um formulário existente
   * @param  int $id    ID do formulário a ser editado
   * @param  array $data Dados do formulário a ser atualizado
   * @return FormDTO     Objeto com os detalhes do formulário atualizado
   */
  public function edit(int $id, array $data) :FormDTO {
  // TODO: Implement edit() method.
    return new FormDTO();
  }

  /**
   * Remove um formulário existente
   * @param  ?int $id ID do formulário a ser removido
   * @return bool
   */
  public function remove(?int $id = null) :bool {
    // TODO: Implement remove() method.
    return false;
  }
}
