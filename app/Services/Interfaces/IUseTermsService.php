<?php

namespace App\Services\Interfaces;

use App\DTO\UseTerms\UseTermsDTO;
use App\Exceptions\BusinessException;

interface IUseTermsService {

  /**
   * Busca os termos de uso mais recentes.
   * @return UseTermsDTO       Retorna objeto de termo de uso.
   * @throws BusinessException Lança exceção se nenhum termo de uso for encontrado.
   */
  public function getNewestUseTerms() :UseTermsDTO;

  /**
   * Valida se o usuário aceitou os termos de uso mais recentes.
   * @param  int  $userId ID do usuário.
   * @return bool         Retorna true se o usuário aceitou os termos de uso mais recentes
   */
  public function validateUseTermsAcceptance(int $userId) :bool;

  /**
   * Aceita um termo de uso
   * @param int $userId        ID do usuário que aceita o termo.
   * @return bool              Retorna true se o termo foi aceito com sucesso
   * @throws BusinessException Lança exceção se o termo de uso não for o mais recente ou se o usuário já tiver aceitado os termos de uso mais recentes.
   */
  public function acceptTerms(int $userId) :bool;

}