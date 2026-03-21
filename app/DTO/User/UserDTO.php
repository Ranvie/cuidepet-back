<?php

namespace App\DTO\User;

use App\DTO\Preference\PreferenceDTO;
use App\DTO\Role\RoleDTO;

class UserDTO{
  public int $id;
  public string $username;
  public string $email;
  public string $password;
  public ?string $imageProfile;
  public ?string $phone;
  public ?string $emailVerifiedAt;
  public bool $active;
  public string $createdAt;
  public string $updatedAt;
  
  /** @var PreferenceDTO[] */
  public array $preference;
  
  /** @var RoleDTO[] */
  public array $roles;
}
