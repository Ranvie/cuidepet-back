<?php

namespace App\ExternalAPI\Address\DTO\Response;

/**
 * DTO de resposta da BrasilAPI para consulta de CEP.
 * @see https://brasilapi.com.br/docs#tag/CEP-V2
 */
class BrasilApiDTO {

  /**
   * CEP (somente dígitos)
   * @var string
   */
  public string $cep;

  /**
   * Sigla do estado (UF)
   * @var string
   */
  public string $state;

  /**
   * Município
   * @var string
   */
  public string $city;

  /**
   * Bairro
   * @var string
   */
  public string $neighborhood;

  /**
   * Logradouro
   * @var string
   */
  public string $street;

  /**
   * Serviço utilizado internamente pela BrasilAPI para consultar o CEP
   * @var string
   */
  public string $service;

  /**
   * Dados de geolocalização do endereço
   * @var BrasilApiDTOLocation
   */
  public BrasilApiDTOLocation $location;

  public function __construct() {
    $this->location = new BrasilApiDTOLocation();
  }
}

/**
 * Dados de geolocalização retornados pela BrasilAPI.
 */
class BrasilApiDTOLocation {

  /**
   * Tipo de geometria geográfica (ex: "Point")
   * @var string
   */
  public string $type;

  /**
   * Coordenadas geográficas do endereço
   * @var BrasilApiDTOLocationCoordinates
   */
  public BrasilApiDTOLocationCoordinates $coordinates;

  public function __construct() {
    $this->coordinates = new BrasilApiDTOLocationCoordinates();
  }
}

/**
 * Coordenadas geográficas retornadas pela BrasilAPI.
 */
class BrasilApiDTOLocationCoordinates {

  /**
   * Longitude
   * @var string
   */
  public string $longitude;

  /**
   * Latitude
   * @var string
   */
  public string $latitude;

}
