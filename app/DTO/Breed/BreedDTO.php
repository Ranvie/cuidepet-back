<?php

namespace App\DTO\Breed;
use App\DTO\Specie\SpecieDTO;

class BreedDTO {
  public int $id;
  public SpecieDTO $specie;
  public string $name;
}
