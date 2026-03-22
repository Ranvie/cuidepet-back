<?php

namespace App\Exceptions;

use App\Http\Response\BusinessResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessException extends Exception {
  
  /**
   * Constrói uma exceção de negócio.
   * @param string $message A mensagem de erro a ser exibida.
   * @param int    $code    O código de status HTTP a ser retornado (padrão: 500).
   */
  public function __construct(string $message = "", int $code = 500) {
    parent::__construct($message, $code);
  }

  /**
   * Renderiza a exceção em uma resposta JSON.
   * @param  Request $request A solicitação HTTP que causou a exceção.
   * @return JsonResponse     A resposta JSON contendo a mensagem de erro e o código de status.
   */
  public function render(Request $request): JsonResponse {
    $response = new BusinessResponse($this->code, $this->message);

    return response()->json($response, $this->code);
  }
}
