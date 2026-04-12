<?php

namespace App\DTO\User;

use App\DTO\Preference\PreferenceDTO;
use App\DTO\Role\RoleDTO;
use Illuminate\Support\Collection;

/**
 * Data Transfer Object (DTO) para representar os dados de um usuário.
 */
class UserDTO{

  /**
   * Identificador do usuário
   * @var int
   */
  public int $id;

  /**
   * Nome de usuário
   * @var string
   */
  public string $username;

  /**
   * Email do usuário
   * @var string
   */
  public string $email;

  /**
   * Imagem de perfil do usuário
   * @var string|null
   */
  public ?string $imageProfile;

  /**
   * URL da imagem de perfil do usuário
   * @var string|null
   */  
  public ?string $imageProfileUrl;

  /**
   * Telefone do usuário
   * @var string|null
   */
  public ?string $phone;

  /**
   * Data de verificação do email
   * @var string|null
   */
  public ?string $emailVerifiedAt;

  /**
   * Status de ativação do usuário
   * @var bool
   */
  public bool $active;

  /**
   * Data de criação do usuário
   * @var string
   */
  public string $createdAt;

  /**
   * Data de atualização do usuário
   * @var string
   */
  public string $updatedAt;

  /**
   * Preferências do usuário
   * @var PreferenceDTO
   */
  public PreferenceDTO $preference;

  /**
   * Lista de perfis de acesso do usuário
   * @var Collection<RoleDTO>
   */
  public Collection $roles;
}
