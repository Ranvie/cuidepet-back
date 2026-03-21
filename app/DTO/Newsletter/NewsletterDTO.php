<?php

namespace App\DTO\Newsletter;
use App\DTO\User\UserDTO;

class NewsletterDTO{

  /**
   * Identificador da newsletter
   * @var int
   */
  public int $id = 0;

  /**
   * Objeto do usuário
   * @var UserDTO
   */
  public UserDTO $user;

  /**
   * Email do usuário que se inscreveu na newsletter
   * @var string
   */  
  public string $email = '';

  /**
   * Status de confirmação do email
   * @var bool
   */
  public bool $emailConfirmed = false;

}
