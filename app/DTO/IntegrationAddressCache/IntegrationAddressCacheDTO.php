<?php

namespace App\DTO\IntegrationAddressCache;

class IntegrationAddressCacheDTO {

  /**
   * Identificador do cache de integração
   * @var int
   */
  public int $id;

  /**
   * Latitude do endereço
   * @var string
   */
  public string $latitude;

  /**
   * Longitude do endereço
   * @var string
   */
  public string $longitude;

  /**
   * CEP do endereço
   * @var string
   */
  public string $zipCode;

  /**
   * Estado do endereço
   * @var string
   */
  public string $state;

  /**
   * Cidade do endereço
   * @var string
   */
  public string $city;

  /**
   * Bairro do endereço
   * @var string|null
   */
  public ?string $neighborhood;

  /**
   * Logradouro do endereço
   * @var string|null
   */
  public ?string $street;

  /**
   * Fonte da integração
   * @var string
   */
  public string $source;

  /**
   * Data de expiração do cache
   * @var string
   */
  public string $expiresAt;

  /**
   * Data de atualização do cache
   * @var string
   */
  public string $updatedAt;
}