<?php

namespace App\ExternalAPI\Integration\Abstract;

use App\Http\Clients\HttpMethod;
use App\Http\Clients\RequestProvider;

/**
 * Classe abstrata para integrações com APIs.
 */
abstract class AbstractIntegrationProvider {
  
  /**
   * URL base do serviço.
   * @var string
   */
  protected string $baseUrl;

  /**
   * Caminho do recurso para consulta.
   * @var string
   */
  protected string $resourcePath;

  /**
   * Número de tentativas até conseguir uma resposta satisfatória.
   * @var int
   */
  protected int $retries = 1;

  /**
   * Tempo de espera entre as tentativas em segundos.
   * @var int
   */
  protected int $wait = 1;

  /**
   * Executa a integração com o serviço externo.
   * @return array Dados da response. Se houver erros, retorna um array com 'success' => false e 'errors' => [...]. Caso contrário, retorna 'success' => true e 'data' => [...].
   */
  public function resolve() :array {  
    $response = $this->tryResolve();

    if(isset($response['errors']) && !empty($response['errors']))
      return [
        'success' => false,
        'errors'  => $response['errors']
      ];

    return [
      'success' => true,
      'data'    => $this->formatResponse($response['data'] ?? [])
    ];
  }

  /**
   * Tenta resolver o endereço fazendo requisições ao serviço externo.
   * @return array|null Dados da response, ou null se não for satisfatório.
   */
  protected function tryResolve() :?array {
    $retries = 0;
    $errors  = [];

    do {
      if($retries > 0)
        sleep($this->wait);

      $response = $this->getRequestProvider()->send();
      $retries++;

    }while(!$this->isResponseSatisfactory($response, $errors) && $retries < $this->retries);

    return [
      'data'   => $response,
      'errors' => $errors
    ];
  }

  /**
   * Configura o provedor de requisição para a integração.
   * @return RequestProvider Configuração do provedor de requisição.
   */
  protected function getRequestProvider() :RequestProvider {
    return (new RequestProvider())
              ->setMethod(HttpMethod::GET)
              ->setDomain($this->getConsultPath())
              ->setSkipSslVerification(env('APP_ENV') === 'local')
              ->setHeaders([
                'Accept'     => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
              ]);
  }

  /**
   * Constrói o caminho completo para a requisição.
   * @return string Caminho completo para a requisição.
   */
  abstract protected function getConsultPath() :string;

  /**
   * Formata os dados em um modelo de response específico.
   * @param  array $responseData Dados a serem formatados.
   * @return array|object        Modelo de response formatada.
   */
  abstract protected function formatResponse(array $responseData) :array|object;

  /**
   * Verifica se a resposta é satisfatória.
   * @param  array|null $responseData Dados a serem verificados.
   * @param  array      $errors       Array para coletar erros de validação, se necessário.
   * @return bool                     Retorna true se os dados forem satisfatórios, caso contrário, false.
   */
  abstract protected function isResponseSatisfactory(array|null $responseData, array &$errors = []) :bool;
}