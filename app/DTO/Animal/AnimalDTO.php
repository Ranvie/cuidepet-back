<?php

namespace App\DTO\Animal;

use App\DTO\Announcement\AnnouncementDTO;
use App\DTO\Breed\BreedDTO;

class AnimalDTO {

  /**
   * Identificador do animal
   * @var int
   */
  public int $id;

  /**
   * Objeto do anúncio vinculado ao animal
   * @var AnnouncementDTO
   */
  public AnnouncementDTO $announcement;

  /**
   * Objeto da raça do animal
   * @var BreedDTO
   */
  public BreedDTO $breed;

  /**
   * Nome do animal
   * @var string
   */
  public string $name;

  /**
   * Gênero do animal
   * @var string
   */
  public string $gender;

  /**
   * Cor do animal
   * @var string
   */
  public string $color;

  /**
   * Tamanho do animal
   * @var string
   */
  public string $size;

  /**
   * Idade do animal
   * @var string
   */
  public string $age;

  /**
   * Indica se o animal possui deficiência
   * @var bool|null
   */
  public ?bool $disability;

  /**
   * Indica se o animal é vacinado
   * @var bool|null
   */
  public ?bool $vaccinated;

  /**
   * Indica se o animal é vermifugado
   * @var bool|null
   */
  public ?bool $dewormed;

  /**
   * Indica se o animal é castrado
   * @var bool|null
   */
  public ?bool $castrated;

  /**
   * Imagem de perfil do animal
   * @var string
   */
  public string $imageProfile;

  /**
   * URL completa da imagem de perfil do animal
   * @var string|null
   */
  public ?string $imageProfileUrl;

  /**
   * Data do último avistamento
   * @var string
   */
  public string $lastSeenDate;
}
