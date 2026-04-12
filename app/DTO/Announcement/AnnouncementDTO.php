<?php

namespace App\DTO\Announcement;

use App\DTO\Form\FormDTO;
use App\DTO\User\UserDTO;
use App\DTO\Address\AddressDTO;
use App\DTO\Animal\AnimalDTO;
use App\DTO\AnnouncementMedia\AnnouncementMediaDTO;
use Illuminate\Support\Collection;

class AnnouncementDTO {

  /**
   * Identificador do anúncio
   * @var int
   */
  public int $id;

  /**
   * Objeto do usuário dono do anúncio
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Identificador do usuário dono do anúncio
   * @var int|null
   */
  public ?int $userId;

  /**
   * Objeto do formulário vinculado ao anúncio
   * @var FormDTO|null
   */
  public ?FormDTO $form;

  /**
   * Objeto do endereço do anúncio
   * @var AddressDTO
   */
  public AddressDTO $address;

  /**
   * Identificador do endereço do anúncio
   * @var int|null
   */
  public ?int $addressId;

  /**
   * Tipo do anúncio
   * @var string
   */
  public string $type;

  /**
   * Descrição do anúncio
   * @var string
   */
  public string $description;

  /**
   * Caminho da imagem principal do anúncio
   * @var ?string
   */
  public ?string $mainImage;

  /**
   * Imagem principal do anúncio
   * @var string|null
   */
  public ?string $mainImageUrl;

  /**
   * Email de contato do anúncio
   * @var string|null
   */
  public ?string $contactEmail;

  /**
   * Telefone de contato do anúncio
   * @var string|null
   */
  public ?string $contactPhone;

  /**
   * Status de ativação do anúncio
   * @var bool
   */
  public bool $active;

  /**
   * Indica se o anúncio está bloqueado
   * @var bool
   */
  public bool $blocked;

  /**
   * Status do anúncio
   * @var string
   */
  public string $status;

  /**
   * Data de criação do anúncio
   * @var string
   */
  public string $createdAt;

  /**
   * Data de atualização do anúncio
   * @var string
   */
  public string $updatedAt;

  /**
   * Objeto do animal vinculado ao anúncio
   * @var AnimalDTO
   */
  public AnimalDTO $animal;

  /**
   * Lista de mídias associadas ao anúncio
   * @var Collection<AnnouncementMediaDTO>
   */
  public Collection $announcementMedia;
}
