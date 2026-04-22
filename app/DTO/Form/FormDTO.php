<?php

namespace App\DTO\Form;

use App\DTO\User\UserDTO;

class FormDTO {

  /**
   * Identificador do formulário
   * @var int
   */
  public int $id;

  /**
   * Identificador do usuário
   * @var int
   */
  public int $userId;

  /**
   * Objeto do usuário dono do formulário
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Título do formulário
   * @var string
   */
  public string $title;

  /**
   * Conteúdo do formulário
   * @var string
   */
  public string $payload;

  /**
   * Status de ativação do formulário
   * @var bool
   */
  public bool $active;

  /**
   * Determina se o formulário está pausado por denúncias
   * @var bool
   */
  public bool $blocked;

  /**
   * Data de criação do formulário
   * @var string
   */
  public string $createdAt;

  /**
   * Data de atualização do formulário
   * @var string
   */
  public string $updatedAt;
}