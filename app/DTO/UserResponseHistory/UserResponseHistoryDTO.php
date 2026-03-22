<?php

namespace App\DTO\UserResponseHistory;

use App\DTO\Announcement\AnnouncementDTO;
use App\DTO\User\UserDTO;

class UserResponseHistoryDTO{

  /**
   * Identificador dos termos de uso
   * @var int
   */
  public int $id;

  /**
   * Identificador do Usuário
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Identificador do anúncio
   * @var AnnouncementDTO
   */
  public AnnouncementDTO $announcement;

  /**
   * Data de expiração da resposta do usuário ao anúncio
   * @var string
   */
  public string $expiresAt;

  /**
   * Data de criação do histórico de resposta
   * @var string
   */  
  public string $createdAt;

}
