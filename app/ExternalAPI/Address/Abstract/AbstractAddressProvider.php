<?php

namespace App\ExternalAPI\Address\Abstract;

use App\ExternalAPI\Integration\Abstract\AbstractIntegrationProvider;
use App\Utils\Functions;

/**
 * Classe base para provedores de endereço, fornecendo a estrutura e métodos comuns para resolver endereços a partir de serviços externos.
 */
abstract class AbstractAddressProvider extends AbstractIntegrationProvider {

  /** 
   * CEP a ser consultado.
   * @var string
   */
  protected string $zipcode;

  /**
   * Identificador do provedor de endereço.
   * @var string|null
   */
  protected ?string $provider = null;

  /**
   * Duração do cache em segundos.
   * @var int|null
   */
  protected ?int $cacheDuration = 60 * 60 * 24 * 30; // 30 dias

  /**
   * Construtor da classe, recebe o CEP a ser consultado e remove quaisquer caracteres não numéricos.
   * @param  string $zipcode CEP a ser consultado.
   */
  public function __construct(string $zipcode) {
    if(!$this->isZipCodeValid($zipcode))
      throw new \InvalidArgumentException("O CEP fornecido é inválido: {$zipcode}");

    $this->zipcode = str_replace('-', '', $zipcode);
  }

  /**
   * Verifica se o CEP é válido.
   * @param  string $zipCode CEP a ser validado.
   * @return bool            Retorna true se o CEP for válido, caso contrário, false.
   */
  protected function isZipCodeValid(string $zipCode) :bool {
    return Functions::isZipCodeValid($zipCode);
  }

}