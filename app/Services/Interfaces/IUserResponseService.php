<?php

namespace App\Services\Interfaces;

use App\DTO\FormResponse\FormResponseDTO;

interface IUserResponseService {

  /**
   * Lista paginada de formulários respondidos por um usuário específico.
   * @param  int $limit  Número de formulários por página.
   * @param  int $page   Número da página atual.
   * @param  int $userId ID do usuário.
   * @return array       Lista paginada de formulários respondidos por um usuário específico.
   */
  public function listUserResponses(int $limit, int $page, int $userId) :array;

  /**
   * Busca formulário respondido por um usuário.
   * @param  int $responseId ID do formulário.
   * @param  int $userId     ID do usuário.
   * @return FormResponseDTO Coleção de formulários do usuário.
   */
  public function getUserResponseById(int $responseId, int $userId) :FormResponseDTO;

  /**
   * Exclui formulários de resposta do usuário.
   * @param  int $responseId ID do formulário a ser excluído.
   * @return bool            True se a exclusão foi bem-sucedida, false caso contrário.
   */
  public function remove(int $responseId) :bool;
}
