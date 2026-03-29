<?php

namespace App\ExternalAPI\Address\DTO\Response;

class AwesomeCepDTO {

  /**
   * CEP (somente dígitos)
   * @var string
   */
  public string $cep;

  /**
   * Tipo do logradouro
   * @var string
   */
  public string $address_type;

  /**
   * Nome do logradouro (sem o tipo)
   * @var string
   */
  public string $address_name;

  /**
   * Logradouro completo (tipo + nome)
   * @var string
   */
  public string $address;

  /**
   * Siglas do estado (UF)
   * @var string
   */
  public string $state;

  /**
   * Bairro
   * @var string
   */
  public string $district;

  /**
   * Latitude
   * @var string
   */
  public string $lat;

  /**
   * Longitude
   * @var string
   */
  public string $lng;

  /**
   * Município
   * @var string
   */
  public string $city;

  /**
   * Código IBGE do município
   * @var string
   */
  public string $city_ibge;

  /**
   * DDD
   * @var string
   */
  public string $ddd;

}
