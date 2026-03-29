<?php

namespace App\ExternalAPI\Address\Provider;

use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use App\ExternalAPI\Address\Abstract\AbstractAddressProvider;

class ViaCepAddressProvider extends AbstractAddressProvider {

  /**
   * Identificador do provedor de endereço.
   * @var string|null
   */
  protected ?string $provider = 'viacep';

  /** 
   * URL base do serviço de endereço.
   * @var string
   */
  protected string $baseUrl = 'https://viacep.com.br';

  /** 
   * Caminho do recurso para consulta de endereço.
   * @var string
   */
  protected string $resourcePath = '/ws/{zipCode}/json/';

  /** 
   * Número de tentativas para resolver o endereço.
   * @var int
   */
  protected int $retries = 1;

  /**
   * Constrói o caminho completo para a requisição de endereço.
   * @param  string $zipCode CEP para consulta do endereço.
   * @return string          Caminho completo para a requisição.
   */
  protected function getZipCodeConsultPath(string $zipCode) :string {
    return str_replace('{zipCode}', $zipCode, $this->baseUrl . $this->resourcePath);
  }

  /**
   * Formata os dados em um cache de endereço
   * @param  array $addressData         Dados do endereço a serem formatados.
   * @return IntegrationAddressCacheDTO Modelo de cache de endereço formatado.
   */
  protected function formatAddress(array $addressData) :IntegrationAddressCacheDTO {
    dd($addressData);
  
    $ret = new IntegrationAddressCacheDTO();
    return $ret;
  }

  /**
   * Verifica se os dados de endereço retornados pelo serviço são satisfatórios.
   * Deve setar $this->responseSatisfactory = true quando a resposta for válida.
   * @param  array|null $addressData Dados do endereço a serem verificados.
   * @return bool                    Retorna true se os dados forem satisfatórios, caso contrário, false.
   */
  protected function isResponseSatisfactory(?array $addressData) :bool {
    $this->responseSatisfactory = true;
    return true;
  }
}