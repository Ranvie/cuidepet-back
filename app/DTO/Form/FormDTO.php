<?php

namespace App\DTO\Form;

use App\DTO\User\UserDTO;

class FormDTO {
  public int $id;
  public UserDTO $user;
  public string $title;
  public string $payload;
  public bool $active;
  public string $createdAt;
  public string $updatedAt;
}