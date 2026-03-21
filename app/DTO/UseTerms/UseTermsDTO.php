<?php

namespace App\DTO\UseTerms;

class UseTermsDTO{

  /**
   * Identificador dos termos de uso
   * @var int
   */
  public int $id = 0;

  /**
   * Título dos termos de uso
   * @var string
   */
  public string $title = '';

  /**
   * Descrição dos termos de uso
   * @var string
   */
  public string $description = '';

  /**
   * Status de ativação do termo de uso
   * @var bool
   */
  public bool $active = false;

  /**
   * Data de criação do termo de uso
   * @var string
   */  
  public string $createdAt = '';

  /**
   * Data de atualização do termo de uso
   * @var string
   */
  public string $updatedAt = '';

}
