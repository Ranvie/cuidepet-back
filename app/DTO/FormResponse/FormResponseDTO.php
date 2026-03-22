<?php

namespace App\DTO\FormResponse;

use App\DTO\User\UserDTO;
use App\DTO\Announcement\AnnouncementDTO;

class FormResponseDTO {

  /**
   * Identificador da resposta do formulário
   * @var int
   */
  public int $id;

  /**
   * Objeto do usuário dono da resposta do formulário
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Objeto do anúncio relacionado à resposta do formulário
   * @var AnnouncementDTO
   */
  public AnnouncementDTO $announcement;

  /**
   * Conteúdo da resposta do formulário
   * @var string
   */
  public string $payload;

  /**
   * Data de criação da resposta do formulário
   * @var string
   */
  public string $createdAt;

}