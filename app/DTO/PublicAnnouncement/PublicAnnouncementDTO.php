<?php

namespace App\DTO\PublicAnnouncement;

use App\DTO\User\UserDTO;
use App\DTO\Address\AddressDTO;

class PublicAnnouncementDTO {

  /**
   * Identificador do anúncio público
   * @var int
   */
  public int $id;

  /**
   * Objeto do usuário dono do anúncio
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Objeto do endereço do anúncio
   * @var AddressDTO
   */
  public AddressDTO $address;

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
   * Imagem principal do anúncio
   * @var string|null
   */
  public ?string $mainImage;

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
   * Contagem de favoritos do anúncio
   * @var int
   */
  public int $favoritesCount;

  /**
   * Contagem de favoritos do anúncio
   * @var bool
   */
  public bool $isFavorited;

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
}
