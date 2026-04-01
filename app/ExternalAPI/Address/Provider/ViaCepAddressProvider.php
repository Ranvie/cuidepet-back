<?php

namespace App\ExternalAPI\Address\Provider;

use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use App\ExternalAPI\Address\Abstract\AbstractAddressProvider;
use App\ExternalAPI\Address\DTO\Response\ViaCepDTO;
use App\Utils\Objectfy;

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
   * @return string Caminho completo para a requisição.
   */
  protected function getConsultPath() :string {
    return str_replace('{zipCode}', $this->zipcode, $this->baseUrl . $this->resourcePath);
  }

  /**
   * Formata os dados em um cache de endereço
   * OBS: A integração da ViaCEP não retorna latitude e longitude, ela servirá como uma populadora de dados para outros provedores
   * @param  array $addressData         Dados do endereço a serem formatados.
   * @return IntegrationAddressCacheDTO Modelo de cache de endereço formatado.
   */
  protected function formatResponse(array $addressData) :IntegrationAddressCacheDTO {
    $apiResponse           = Objectfy::arrayToClass($addressData, ViaCepDTO::class);
    $address               = new IntegrationAddressCacheDTO();
    $address->zipcode      = $apiResponse->cep;
    $address->state        = $apiResponse->uf;
    $address->city         = $apiResponse->localidade;
    $address->neighborhood = $apiResponse->bairro;
    $address->street       = $apiResponse->logradouro;
    $address->source       = $this->provider;
    $address->expiresAt    = date('Y-m-d H:i:s', time() + ($this->cacheDuration ?? 0));

    return $address;
  }

  /**
   * Verifica se os dados de endereço retornados pelo serviço são satisfatórios.
   * Deve setar $this->responseSatisfactory = true quando a resposta for válida.
   * @param  array|null $addressData Dados do endereço a serem verificados.
   * @param  array      $errors      Array para coletar erros de validação, se necessário.
   * @return bool                    Retorna true se os dados forem satisfatórios, caso contrário, false.
   */
  protected function isResponseSatisfactory(?array $addressData, array &$errors = []) :bool {
    return true;
  }
}