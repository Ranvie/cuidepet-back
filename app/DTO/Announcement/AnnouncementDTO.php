<?php

namespace App\DTO\Announcement;

use App\DTO\Form\FormDTO;
use App\DTO\User\UserDTO;
use App\DTO\Address\AddressDTO;

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
