<?php

namespace App\ExternalAPI\Address\Provider;

use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use App\ExternalAPI\Address\Abstract\AbstractAddressProvider;
use App\ExternalAPI\Address\DTO\Response\AwesomeCepDTO;
use App\Utils\Objectfy;
use App\Utils\RequiredFieldsValidator;

class AwesomeCepAddressProvider extends AbstractAddressProvider {

  /**
   * Identificador do provedor de endereço.
   * @var string|null
   */
  protected ?string $provider = 'awesomeapi';

  /** 
   * URL base do serviço de endereço.
   * @var string
   */
  protected string $baseUrl = 'https://cep.awesomeapi.com.br';

  /** 
   * Caminho do recurso para consulta de endereço.
   * @var string
   */
  protected string $resourcePath = '/json/{zipCode}';

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
  protected function getConsultPath() :string {
    return str_replace('{zipCode}', $this->zipcode, $this->baseUrl . $this->resourcePath);
  }

  /**
   * Formata os dados em um cache de endereço
   * @param  array $addressData         Dados do endereço a serem formatados.
   * @return IntegrationAddressCacheDTO Modelo de cache de endereço formatado.
   */
  protected function formatResponse(array $addressData) :IntegrationAddressCacheDTO {
    $apiResponse           = Objectfy::arrayToClass($addressData, AwesomeCepDTO::class);
    $address               = new IntegrationAddressCacheDTO();
    $address->zipcode      = $apiResponse->cep;
    $address->state        = $apiResponse->state;
    $address->city         = $apiResponse->city;
    $address->neighborhood = $apiResponse->district;
    $address->street       = $apiResponse->address;
    $address->latitude     = $apiResponse->lat;
    $address->longitude    = $apiResponse->lng;
    $address->source       = $this->provider;
    $address->expiresAt    = date('Y-m-d H:i:s', time() + ($this->cacheDuration ?? 0));

    return $address;
  }

  /**
   * Verifica se os dados de endereço retornados pelo serviço são satisfatórios.
   * Deve setar $this->responseSatisfactory = true quando a resposta for válida.
   * @param  array|object|null $addressData Dados do endereço a serem verificados.
   * @param  array             $errors      Array para acumular mensagens de erro, caso a resposta não seja satisfatória.
   * @return bool                           Retorna true se os dados forem satisfatórios, caso contrário, false.
   */
  protected function isResponseSatisfactory(array|object|null $addressData, array &$errors = []) :bool {
    if(!$addressData){
      $errors[] = "A API não retornou dados válidos.";
      return false;
    }

    if(RequiredFieldsValidator::validate($addressData, ['cep','state','city','lat','lng'])){
      $errors = [];
      return true;
    }

    $errors[] = "A resposta da API não contém os campos necessários ou está em formato inesperado.";
    return true;
  }
}