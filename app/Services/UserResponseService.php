<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\FormResponse\FormResponseDTO;
use App\Exceptions\BusinessException;
use App\Models\FormResponseModel;
use App\Services\Interfaces\IUserResponseService;

/**
 * Serviço de gerenciamento de resposta do usuário.
 * Fornece métodos para criar, editar, listar e remover formulários, além de validações relacionadas a formulários.
 */
class UserResponseService implements IUserResponseService {

  /**
   * Método Construtor
   * @param FormResponseModel $obFormResponseModel
   */
  public function __construct(
    private FormResponseModel $obFormResponseModel
  ) {}

   /**
   * Lista paginada de formulários respondidos por um usuário específico.
   * @param  int $limit  Número de formulários por página.
   * @param  int $page   Número da página atual.
   * @param  int $userId ID do usuário.
   * @return array       Lista paginada de formulários respondidos por um usuário específico.
   */
  public function listUserResponses(int $limit, int $page, int $userId) :array {
    return $this->obFormResponseModel->list($limit, $page, filters: [new Filter('user_id', '=', $userId)]);
  }

  /**
   * Busca formulário respondido por um usuário.
   * @param  int $responseId ID do formulário.
   * @return FormResponseDTO Retorna formulário de resposta do usuário.
   */
  public function getUserResponseById(int $responseId) :FormResponseDTO {
    $obFormResponseDTO = $this->obFormResponseModel->getById($responseId);

    if(!$obFormResponseDTO instanceof FormResponseDTO)
      throw new BusinessException("A resposta solicitada não foi encontrada.", 404);

    return $obFormResponseDTO;
  }

  /**
   * Exclui formulários de resposta do usuário.
   * @param  int $responseId ID do formulário a ser excluído.
   * @return bool            True se a exclusão foi bem-sucedida, false caso contrário.
   */
  public function remove(int $responseId) :bool {
    return $this->obFormResponseModel->remove($responseId);
  }

}
