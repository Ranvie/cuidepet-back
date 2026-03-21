<?php

namespace App\DTO\Role;

class RoleDTO {

  /**
   * Identificador do perfil de acesso
   * @var int
   */
  public int $id;

  /**
   * Nome do perfil de acesso
   * @var string
   */
  public string $name;

  /**
   * Descrição do perfil de acesso
   * @var string
   */
  public string $description;
}
