<?php

namespace App\DTO\UseTermsAcceptance;

use App\DTO\User\UserDTO;
use App\DTO\UseTerms\UseTermsDTO;

class UseTermsAcceptanceDTO{

  /**
   * Identificador do aceite dos termos de uso
   * @var int
   */
  public int $id;

  /**
   * Usuário que aceitou os termos de uso
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Termos de uso aceitos
   * @var UseTermsDTO
   */
  public UseTermsDTO $useTerms;

  /**
   * Data de atualização do termo de uso
   * @var string
   */
  public string $acceptedAt;

}
