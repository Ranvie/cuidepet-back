<?php

namespace App\Services;

use App\Models\UseTermsAcceptanceModel;
use App\Models\UseTermsModel;
use App\Services\Interfaces\IUseTermsService;

class UseTermsService implements IUseTermsService {

  /**
   * Método Construtor
   * @param UseTermsModel $obUseTermsModel Modelo de dados para termos de uso
   */
  public function __construct(
    private UseTermsModel $obUseTermsModel,
    private UseTermsAcceptanceModel $obUseTermsAcceptanceModel
  ) {}

  /**
   * Obtém um registro por ID.
   * @param  int    $userId ID do usuário.
   * @return object Objeto com os detalhes do registro.
   */
  public function getNewestUseTerms(?int $userId = null) :object {
    return $this->obUseTermsModel->getLatestUseTerms($userId);
  }

  /**
   * Aceita um termo de uso
   * @param int $useTermId ID do Termo de uso.
   * @param int $userId    ID do usuário que aceita o termo.
   * @return bool          Retorna true se o termo foi aceito com sucesso, caso contrário, retorna false.
   */
  public function acceptTerm(int $useTermId, int $userId) :bool {
    $this->obUseTermsAcceptanceModel->create([
      'use_term_id' => $useTermId,
      'user_id' => $userId
    ]);

    return true;
  }
  
}
