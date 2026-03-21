<?php

namespace App\DTO\Announcement;

use App\DTO\Form\FormDTO;
use App\DTO\User\UserDTO;
use App\DTO\Address\AddressDTO;

class AnnouncementDTO {
  public int $id;
  public UserDTO $user;
  public ?FormDTO $form;
  public AddressDTO $address;
  public string $type;
  public string $description;
  public ?string $mainImage;
  public ?string $contactEmail;
  public ?string $contactPhone;
  public bool $active;
  public bool $blocked;
  public string $status;
  public string $createdAt;
  public string $updatedAt;
}
