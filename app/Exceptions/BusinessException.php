<?php

namespace App\Exceptions;

use App\Http\Response\BusinessResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A classe BusinessException é uma exceção personalizada que estende a classe base Exception do PHP.
 * Ela é usada para representar erros de negócio específicos em uma aplicação.
 */
class BusinessException extends Exception {
  
  /**
   * @var array $flags Um array de flags adicionais que podem ser usadas para fornecer informações extras sobre a exceção.
   */
  protected array $flags;

  /**
   * Constrói uma exceção de negócio.
   * @param string $message A mensagem de erro a ser exibida.
   * @param int    $code    O código de status HTTP a ser retornado (padrão: 500).
   */
  public function __construct(string $message = "", int $code = 500, array $flags = []) {
    parent::__construct($message, $code);
    $this->flags = $flags;
  }

  /**
   * Renderiza a exceção em uma resposta JSON.
   * @param  Request $request A solicitação HTTP que causou a exceção.
   * @return JsonResponse     A resposta JSON contendo a mensagem de erro e o código de status.
   */
  public function render(Request $request): JsonResponse {
    $response = new BusinessResponse($this->code, $this->message, $this->flags);

    return response()->json($response, $this->code);
  }
}
