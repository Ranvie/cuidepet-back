<?php

namespace App\ExternalAPI\Address\DTO\Response;

class ViaCepDTO {

  /**
   * CEP
   * @var string
   */
  public string $cep;

  /**
   * Logradouro
   * @var string
   */
  public string $logradouro;

  /**
   * Complemento
   * @var string
   */
  public string $complemento;

  /**
   * Unidade
   * @var string
   */
  public string $unidade;

  /**
   * Bairro
   * @var string
   */
  public string $bairro;

  /**
   * Município
   * @var string
   */
  public string $localidade;

  /**
   * Sigla do estado (UF)
   * @var string
   */
  public string $uf;

  /**
   * Nome do estado
   * @var string
   */
  public string $estado;

  /**
   * Região
   * @var string
   */
  public string $regiao;

  /**
   * Código IBGE do município
   * @var string
   */
  public string $ibge;

  /**
   * Código GIA (Guia Nacional de Recolhimento de Tributos Estaduais)
   * @var string
   */
  public string $gia;

  /**
   * DDD
   * @var string
   */
  public string $ddd;

  /**
   * Código SIAFI do município
   * @var string
   */
  public string $siafi;

}