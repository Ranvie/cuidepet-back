<?php

namespace App\ExternalAPI\Address\Abstract;

use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;
use App\Http\Clients\HttpMethod;
use App\Http\Clients\RequestProvider;

/**
 * Classe base para provedores de endereço, fornecendo a estrutura e métodos comuns para resolver endereços a partir de serviços externos.
 */
abstract class AbstractAddressProvider {

  /**
   * URL base do serviço de endereço.
   * @var string
   */
  protected string $baseUrl;

  /**
   * Caminho do recurso para consulta de endereço.
   * @var string
   */
  protected string $resourcePath;

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
   * Número de tentativas para resolver o endereço.
   * @var int
   */
  protected int $retries = 3;

  /**
   * Tempo de espera entre as tentativas em segundos.
   * @var int
   */
  protected int $wait = 1;

  /**
   * Indica se a resposta do serviço é satisfatória.
   * @var bool
   */
  protected bool $responseSatisfactory = false;

  /**
   * Obtém os dados de endereço a partir de um serviço usando o CEP.
   * @param  string $zipCode                 CEP para consulta do endereço.
   * @return IntegrationAddressCacheDTO|null Dados do endereço retornados pelo serviço.
   */
  public function resolve(string $zipCode) :?IntegrationAddressCacheDTO {
    if(!$this->isZipCodeValid($zipCode))
      return null;
  
    $path     = $this->getZipCodeConsultPath($zipCode);
    $response = $this->tryResolve($path);

    if(!$this->responseSatisfactory)
      return null;

    return $this->formatAddress($response);
  }

  /**
   * Tenta resolver o endereço fazendo requisições ao serviço externo.
   * @param  string $path Caminho completo para a requisição de endereço.
   * @return array|null   Dados do endereço retornados pelo serviço, ou null se não for satisfatório.
   */
  protected function tryResolve(string $path) :?array {
    $retries = 0;
    do {
      $retries++;
      $response = (new RequestProvider())
                    ->setMethod(HttpMethod::GET)
                    ->setDomain($path)
                    ->setSkipSslVerification(env('APP_ENV') === 'local')
                    ->setHeaders([
                      'Accept'     => 'application/json',
                      'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
                    ])
                    ->send();

      if($retries > 1 && $retries < $this->retries)
        sleep($this->wait);

    }while(!$this->isResponseSatisfactory($response) && $retries < $this->retries);

    return $response;
  }

  /**
   * Constrói o caminho completo para a requisição de endereço.
   * @param  string $zipCode CEP para consulta do endereço.
   * @return string          Caminho completo para a requisição.
   */
  abstract protected function getZipCodeConsultPath(string $zipCode) :string;

  /**
   * Verifica se o CEP é válido.
   * @param  string $zipCode CEP a ser validado.
   * @return bool            Retorna true se o CEP for válido, caso contrário, false.
   */
  protected function isZipCodeValid(string $zipCode) :bool {
    return preg_match('/^\d{5}\d{3}$/', $zipCode);
  }

  /**
   * Formata os dados em um cache de endereço
   * @param  array $addressData         Dados do endereço a serem formatados.
   * @return IntegrationAddressCacheDTO Modelo de cache de endereço formatado.
   */
  abstract protected function formatAddress(array $addressData) :IntegrationAddressCacheDTO;

  /**
   * Verifica se os dados de endereço retornados pelo serviço são satisfatórios.
   * Deve setar $this->responseSatisfactory = true quando a resposta for válida.
   * @param  array|null $addressData Dados do endereço a serem verificados.
   * @return bool                    Retorna true se os dados forem satisfatórios, caso contrário, false.
   */
  abstract protected function isResponseSatisfactory(?array $addressData) :bool;

}