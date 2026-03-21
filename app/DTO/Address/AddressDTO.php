<?php

namespace App\DTO\Address;
use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;

class AddressDTO {

	/**
	 * Identificador do endereço
	 * @var int
	 */
	public int $id;

	/**
	 * Cache de integração do endereço
	 * @var IntegrationAddressCacheDTO
	 */
	public IntegrationAddressCacheDTO $integrationCache;

	/**
	 * Logradouro
	 * @var string
	 */
	public string $street;

	/**
	 * Número do endereço
	 * @var string|null
	 */
	public ?string $number;

	/**
	 * Complemento do endereço
	 * @var string|null
	 */
	public ?string $complement;

	/**
	 * Bairro
	 * @var string
	 */
	public string $neighborhood;

	/**
	 * Cidade
	 * @var string
	 */
	public string $city;
}