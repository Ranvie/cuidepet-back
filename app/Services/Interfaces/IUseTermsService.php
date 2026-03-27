<?php

namespace App\Services\Interfaces;

interface IUseTermsService {

  /**
   * Obtém um registro por ID.
   * @param  int    $userId ID do usuário.
   * @return object Objeto com os detalhes do registro.
   */
  public function getNewestUseTerms(?int $userId = null) :object;

  /**
   * Aceita um termo de uso
   * @param int $useTermId ID do Termo de uso.
   * @param int $userId    ID do usuário que aceita o termo.
   * @return bool          Retorna true se o termo foi aceito com sucesso, caso contrário, retorna false.
   */
  public function acceptTerm(int $useTermId, int $userId) :bool;

}