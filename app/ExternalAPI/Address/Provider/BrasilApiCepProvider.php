<?php

namespace App\ExternalAPI\Address\Provider;

use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use \App\ExternalAPI\Address\DTO\Response\BrasilApiDTO;
use App\ExternalAPI\Address\Abstract\AbstractAddressProvider;
use App\Utils\Objectfy;

class BrasilApiCepProvider extends AbstractAddressProvider {

  /**
   * Identificador do provedor de endereço.
   * @var string|null
   */
  protected ?string $provider = 'brasilapi';

  /** 
   * URL base do serviço de endereço.
   * @var string
   */
  protected string $baseUrl = 'https://brasilapi.com.br';

  /** 
   * Caminho do recurso para consulta de endereço.
   * @var string
   */
  protected string $resourcePath = '/api/cep/v2/{zipCode}';

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
    $apiResponse           = Objectfy::arrayToClass($addressData, BrasilApiDTO::class);
    $address               = new IntegrationAddressCacheDTO();
    $address->zipCode      = $apiResponse->cep;
    $address->state        = $apiResponse->state;
    $address->city         = $apiResponse->city;
    $address->neighborhood = $apiResponse->neighborhood;
    $address->street       = $apiResponse->street;
    $address->latitude     = $apiResponse->location->coordinates->latitude;
    $address->longitude    = $apiResponse->location->coordinates->longitude;
    $address->source       = $this->provider;
    $address->expiresAt    = date('Y-m-d H:i:s', time() + ($this->cacheDuration ?? 0));
    $address->updatedAt    = date('Y-m-d H:i:s');

    return $address;
  }

  /**
   * Verifica se os dados de endereço retornados pelo serviço são satisfatórios.
   * Deve setar $this->responseSatisfactory = true quando a resposta for válida.
   * @param  array|null $addressData Dados do endereço a serem verificados.
   * @return bool                    Retorna true se os dados forem satisfatórios, caso contrário, false.
   */
  protected function isResponseSatisfactory(?array $addressData) :bool {
    if(!$addressData)
      return false;
    
    if($this->validateRequiredFields($addressData)) {
      $this->responseSatisfactory = true;
      return true;
    }

    return true;
  }

  //TODO: Implementar uma forma mais automática, onde passa um array e ele percorre a response validando se os campos do array estão na response
  /**
   * Valida se os campos obrigatórios estão presentes e não estão vazios.
   * @param  array $addressData Dados do endereço a serem validados.
   * @return bool               Retorna true se os campos obrigatórios forem válidos, caso contrário, false.
   */
  private function validateRequiredFields(array $addressData) {
    $requiredFields = ['cep', 'state', 'city', 'neighborhood', 'street', 'location'];
    foreach($requiredFields as $field) {
      if(empty($addressData[$field]))
        return false;
    }

    if(empty($addressData['location']['coordinates']['latitude']) || empty($addressData['location']['coordinates']['longitude']))
      return false;

    return true;
  }
}