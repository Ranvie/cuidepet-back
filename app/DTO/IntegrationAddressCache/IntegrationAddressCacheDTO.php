<?php

namespace App\DTO\IntegrationAddressCache;
use DateTime;

class IntegrationAddressCacheDTO {
  public int $id;
  public string $latitude;
  public string $longitude;
  public string $zipCode;
  public string $state;
  public string $city;
  public ?string $neighborhood;
  public ?string $street;
  public string $source;
  public DateTime $expiresAt;
  public DateTime $updatedAt;
}