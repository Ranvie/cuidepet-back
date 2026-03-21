<?php

namespace App\DTO\Breed;
use App\DTO\Specie\SpecieDTO;

class BreedDTO {

  /**
   * Identificador da raça
   * @var int
   */
  public int $id;

  /**
   * Objeto da espécie vinculada à raça
   * @var SpecieDTO
   */
  public SpecieDTO $specie;

  /**
   * Nome da raça
   * @var string
   */
  public string $name;
}
