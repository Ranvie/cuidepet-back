<?php

namespace App\ExternalAPI\Address\Provider;

use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;

/**
 * Provedor de endereço externo, responsável por resolver endereços a partir de serviços externos.
 * Esta classe é uma implementação concreta que pode ser utilizada para integrar com APIs de terceiros.
 */
class ExternalAddressProvider {

  /**
   * Mapeamento dos provedores de endereço a serem usados.
   * @var array
   */
  protected array $providers = [
    'awesomeapi' => AwesomeCepAddressProvider::class,
    'brasilapi'  => BrasilApiCepProvider::class
  ];

  /**
   * Obtém os dados de endereço a partir de um serviço externo usando o CEP.
   * @param  string $zipCode             CEP para consulta do endereço.
   * @return ?IntegrationAddressCacheDTO Dados do endereço retornados pelo serviço externo.
   */
  public function resolve(string $zipCode) :?IntegrationAddressCacheDTO {
    foreach($this->providers as $providerClass) {
      $provider    = new $providerClass($zipCode);
      $addressData = $provider->resolve();
      
      if($addressData['success'] && $addressData['data'])
        return $addressData['data'];
    }

    return null;
  }

}