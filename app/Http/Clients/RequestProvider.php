<?php

namespace App\Http\Clients;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Http;
use \Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

/**
 * Enum para representar os métodos HTTP suportados.
 */
enum HttpMethod :string {
  case GET   = 'GET';
  case POST  = 'POST';
  case PUT   = 'PUT';
  case PATCH = 'PATCH';
}

/**
 * Classe responsável por construir e enviar requisições para serviços de endereço.
 * Esta classe encapsula a lógica de configuração da requisição, incluindo URL, método HTTP, headers, corpo e autenticação.
 */
class RequestProvider {

  /**
   * Método HTTP a ser utilizado na requisição.
   * @var HttpMethod
   */
  protected HttpMethod $method = HttpMethod::GET;

  /**
   * URL do serviço de endereço.
   * @var string|null
   */
  protected ?string $serviceUrl = null;

  /**
   * Corpo da requisição.
   * @var string|null
   */
  protected ?string $body = null;

  /**
   * Chave da API.
   * @var string|null
   */
  protected ?string $apiKey = null;

  /**
   * Segredo da API.
   * @var string|null
   */
  protected ?string $apiSecret = null;

  /**
   * Headers da requisição.
   * @var array
   */
  protected array $headers = [];

  /**
   * Tempo limite para a requisição em segundos.
   * @var int
   */
  protected int $timeout = 10;

  /**
   * Indica se a verificação SSL deve ser ignorada.
   * USE APENAS EM AMBIENTES DE DESENVOLVIMENTO OU TESTE, POIS IGNORAR A VERIFICAÇÃO SSL PODE EXPOR A REQUISIÇÃO A ATAQUES DE INTERCEPTAÇÃO (MITM).
   * @var bool
   */
  protected bool $skipSslVerification = false;

  /**
   * IP de origem a ser utilizado pelo cURL para enviar a requisição.
   * @var string|null
   */
  protected ?string $sourceIp = null;

  /**
   * Define a URL do serviço de endereço.
   * @param  string $url URL do serviço de endereço.
   * @return self        Retorna a instância atual para encadeamento de métodos.
   */
  public function setDomain(string $url) :self {
    $this->serviceUrl = $url;
    return $this;
  }

  /**
   * Define as credenciais de autenticação para a requisição.
   * @param  string $apiKey    Chave da API.
   * @param  string $apiSecret Segredo da API.
   * @return self              Retorna a instância atual para encadeamento de métodos.
   */
  public function setAuthorization(string $apiKey, string $apiSecret) :self {
    $this->apiKey    = $apiKey;
    $this->apiSecret = $apiSecret;
    return $this;
  }

  /**
   * Define o corpo da requisição.
   * @param  string $body Corpo da requisição em formato JSON.
   * @return self         Retorna a instância atual para encadeamento de métodos.
   */
  public function setRequestBody(string $body) :self {
    $this->body = $body;
    return $this;
  }

  /**
   * Define o método HTTP a ser utilizado na requisição.
   * @param  HttpMethod $method Método HTTP (GET, POST, PUT, PATCH).
   * @return self               Retorna a instância atual para encadeamento de métodos.
   */
  public function setMethod(HttpMethod $method) :self {
    $this->method = $method;
    return $this;
  }

  /**
   * Define os headers da requisição.
   * @param  array $headers Array associativo de headers (chave => valor).
   * @return self           Retorna a instância atual para encadeamento de métodos.
   */
  public function setHeaders(array $headers) :self {
    $this->headers = $headers;
    return $this;
  }

  /**
   * Define o tempo limite para a requisição.
   * @param  int $timeout Tempo limite em segundos.
   * @return self         Retorna a instância atual para encadeamento de métodos.
   */
  public function setTimeout(int $timeout) :self {
    $this->timeout = $timeout;
    return $this;
  }

  public function setSkipSslVerification(bool $skip) :self {
    $this->skipSslVerification = $skip;
    return $this;
  }

  /**
   * Define o IP de origem que o cURL deve utilizar para enviar a requisição.
   * @param  string $ip Endereço IP de origem (ex: '192.168.1.100').
   * @return self       Retorna a instância atual para encadeamento de métodos.
   */
  public function setSourceIp(string $ip) :self {
    $this->sourceIp = $ip;
    return $this;
  }

  /**
   * Envia a requisição para o serviço de endereço e retorna a resposta.
   * @return array Dados retornados pelo serviço de endereço.
   */
  public function send() :array {
    $request = Http::timeout($this->timeout);
    
    $bodyData = $this->body 
      ? (json_decode($this->body, true) ?? []) 
      : [];

    $request = $this->requestAuthorization($request);

    $request = !empty($this->serviceUrl) 
      ? $request->withHeaders($this->headers)
      : $request;

    if($this->skipSslVerification) {
      $request = $request->withoutVerifying();
    }

    if($this->sourceIp) {
      $request = $request->withOptions(['curl' => [CURLOPT_INTERFACE => $this->sourceIp]]);
    }

    $response = $this->getResponse($request, $bodyData);

    return $response->successful() ? ($response->json() ?? []) : [];
  }

  /**
   * Aplica a autenticação à requisição, se as credenciais estiverem definidas.
   * @param  PendingRequest $request Instância da requisição pendente.
   * @return PendingRequest          Instância da requisição com autenticação aplicada, se necessário.
   */
  protected function requestAuthorization(PendingRequest $request) :PendingRequest {
    if($this->apiKey && $this->apiSecret) 
      return $request->withBasicAuth($this->apiKey, $this->apiSecret);

    if($this->apiKey) 
      return $request->withToken($this->apiKey);

    return $request;
  }

  /**
   * Configura o método HTTP da requisição com base no valor definido em $this->method.
   * @param  PendingRequest $request  Instância da requisição pendente.
   * @param  array          $bodyData Dados do corpo da requisição a serem enviados.
   * @return Response                 Instância da requisição configurada com o método HTTP correto.
   * @throws BusinessException        Exceção lançada se o método HTTP definido não for suportado.
   */
  protected function getResponse(PendingRequest $request, array $bodyData) :Response {
    $response = match ($this->method) {
      HttpMethod::GET   => $request->get($this->removeQueryParams($this->serviceUrl), $this->getQueryParams($this->serviceUrl) ?? []),
      HttpMethod::POST  => $request->post($this->serviceUrl, $bodyData),
      HttpMethod::PUT   => $request->put($this->serviceUrl, $bodyData),
      HttpMethod::PATCH => $request->patch($this->serviceUrl, $bodyData),
      default           => throw new BusinessException("Método HTTP não suportado: {$this->method->value}", 400)
    };

    return $response;
  }

  /**
   * Extrai os parâmetros de consulta (query parameters) de uma URL.
   * @param  string $url URL da qual os parâmetros de consulta devem ser extraídos.
   * @return array|null  Array associativo dos parâmetros de consulta ou null se não houver parâmetros.
   */
  protected function getQueryParams(string $url) :?array {
    $parts = parse_url($url);
    if (!isset($parts['query'])) {
      return null;
    }

    parse_str($parts['query'], $queryParams);
    return $queryParams;
  }

  /**
   * Remove os parâmetros de consulta (query parameters) de uma URL, retornando apenas a parte base da URL.
   * @param  string $url URL da qual os parâmetros de consulta devem ser removidos.
   * @return string      URL sem os parâmetros de consulta.
   */
  protected function removeQueryParams(string $url) :string {
    return explode('?', $url)[0];
  }

}