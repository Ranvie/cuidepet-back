<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Validate {

  /**
   * Método Construtor
   */
  public function __construct(
  ) {}
  
  /**
   * Aplica regras de validação customizadas para rotas do controller
   * @param Request  $request Objeto Request contendo os dados da requisição HTTP.
   * @param Closure  $next    Função de callback para passar a requisição para o próximo
   * @param mixed ...$params  Parâmetros adicionais para a validação de pertencimento, como nome do modelo, chave estrangeira e métodos a serem aplicados.
   * @return Response         Retorna a resposta HTTP após a validação, ou uma resposta de erro caso a validação falhe.
   */
  public function handle(Request $request, Closure $next, mixed ...$params) :Response {
    $params = $this->parseMiddlewareParams($params);

    $action         = $request->route()->getActionMethod() ?? '';
    $validatorClass = $params['policy'] ?? '';
    $ignoreMethods  = explode('|', $params['ignored'] ?? '');

    if(\in_array($action, $ignoreMethods))
      return $next($request);

    try {
      $validator = app("App\RouteRules\\$validatorClass", ['request' => $request, 'params' => $params]);

      if(!method_exists($validator, 'validate'))
        throw new BusinessException("O validador $validatorClass não possui o método validate.", 500);

      $validator->validate();
    }
    catch (BusinessException $e) {
      throw $e;
    }
    catch (\Exception $e) {
      throw new BusinessException("A classe $validatorClass não existe.", 500);
    }

    return $next($request);
  }

  /**
   * Responsável por estruturar os parâmetros do middleware
   * @param  array $params Parâmetros brutos passados para o middleware, no formato ['key=value']
   * @return array         Parâmetros estruturados em um array associativo, no formato ['key' => 'value']
   */
  private function parseMiddlewareParams(array $params) :array {
    $parsed = [];
    foreach ($params as $param) {
      [$key, $value] = array_pad(explode('=', $param, 2), 2, null);
      $parsed[$key] = $value;
    }

    return $parsed;
  }

}