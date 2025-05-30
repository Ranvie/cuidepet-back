<?php

namespace App\DTO\Animal;

use App\DTO\Breed\BreedDTO;
use App\DTO\Specie\SpecieDTO;

class AnimalDTO {

    public int $id;
    public string $name;
    public string $gender;
    public string $color;
    public string $size;
    public string $age;
    public ?bool $disability;
    public ?bool $vaccinated;
    public ?bool $dewormed;
    public ?bool $castrated;
    public string $imageProfile;
    public string $lastSeenDate;
}
