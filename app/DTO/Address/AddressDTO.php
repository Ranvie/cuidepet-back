<?php

namespace App\DTO\Address;
use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;

class AddressDTO {
	public int $id;
	public IntegrationAddressCacheDTO $integrationCache;
	public string $street;
	public ?string $number;
	public ?string $complement;
	public string $neighborhood;
	public string $city;
}