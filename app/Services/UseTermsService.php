<?php

namespace App\Services;

use App\DTO\UseTerms\UseTermsDTO;
use App\Exceptions\BusinessException;
use App\Models\UserModel;
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
   * Busca os termos de uso mais recentes.
   * @return UseTermsDTO       Retorna objeto de termo de uso.
   * @throws BusinessException Lança exceção se nenhum termo de uso for encontrado.
   */
  public function getNewestUseTerms() :UseTermsDTO {
    $newestUseTerms = $this->obUseTermsModel->getLatestUseTerms();

    if(!$newestUseTerms instanceof UseTermsDTO)
      throw new BusinessException("Nenhum termo de uso encontrado.", 404);

    return $newestUseTerms;
  }

  /**
   * Valida se o usuário aceitou os termos de uso mais recentes.
   * @param  int  $userId ID do usuário.
   * @return bool         Retorna true se o usuário aceitou os termos de uso mais recentes
   */
  public function validateUseTermsAcceptance(int $userId) :bool {
    $newestUseTerms = $this->obUseTermsModel->getLatestUseTerms(parse: false);
    $obUserModel    = $newestUseTerms->users()
      ->wherePivot('user_id', $userId)
      ->first();

    return $obUserModel instanceof UserModel;
  }

  /**
   * Aceita um termo de uso
   * @param int $userId        ID do usuário que aceita o termo.
   * @return bool              Retorna true se o termo foi aceito com sucesso
   * @throws BusinessException Lança exceção se o termo de uso não for o mais recente ou se o usuário já tiver aceitado os termos de uso mais recentes.
   */
  public function acceptTerms(int $userId) :bool {
    $useTermsIsAccepted = $this->validateUseTermsAcceptance($userId);

    if($useTermsIsAccepted)
      throw new BusinessException("O usuário já aceitou os termos de uso mais recentes.", 400);

    $this->obUseTermsAcceptanceModel->create([
      'use_terms_id' => $this->getNewestUseTerms()->id,
      'user_id'      => $userId,
      'accepted_at'  => now()
    ], parse: false);

    return true;
  }
  
}
