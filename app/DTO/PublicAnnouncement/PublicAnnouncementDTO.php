<?php

namespace App\DTO\PublicAnnouncement;

use App\DTO\User\UserDTO;
use App\DTO\Address\AddressDTO;

class PublicAnnouncementDTO {
  public int $id;
  public UserDTO $user;
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
