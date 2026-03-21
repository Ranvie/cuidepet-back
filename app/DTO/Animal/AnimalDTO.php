<?php

namespace App\DTO\Animal;

use App\DTO\Announcement\AnnouncementDTO;
use App\DTO\Breed\BreedDTO;

class AnimalDTO {
  public int $id;
  public AnnouncementDTO $announcement;
  public BreedDTO $breed;
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
