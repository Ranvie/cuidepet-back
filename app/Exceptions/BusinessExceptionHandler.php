<?php

namespace App\Exceptions;

use App\Http\Response\BusinessResponse;
use stdClass;
use Exception;
use Illuminate\Http\JsonResponse;

use \Illuminate\Validation\ValidationException;
use \Illuminate\Auth\AuthenticationException;
use \Illuminate\Auth\Access\AuthorizationException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BusinessExceptionHandler {

  /**
   * Status de erro
   * @var int
   */
  private int $status;

  /**
   * Exceção a ser manipulada
   * @var Exception
   */
  private Exception $exception;

  /**
   * Constrói um manipulador de exceções de negócio.
   * @param Exception $exception A exceção a ser manipulada.
   */
  public function __construct(Exception $exception) {
    $this->exception = $exception;
    $this->status    = $this->mapErrorCode($this->exception);
  }

  /**
   * Renderiza a resposta de erro para a exceção de negócio.
   * @return JsonResponse A resposta JSON contendo a mensagem de erro e, se aplicável, os detalhes dos erros de validação.
   */
  public function render(): JsonResponse {
    $responseData = new stdClass();
    $responseData->message = $this->exception->getMessage();
    
    if($this->exception instanceof ValidationException) {
      $responseData->errors = $this->exception->errors();
    }
    
    $response = new BusinessResponse($this->status, $responseData);
    return $response->build();
  }

  /**
   * Mapeia a exceção para um código de status HTTP apropriado.
   * @param  Exception $exception A exceção a ser mapeada.
   * @return int                  O código de status HTTP correspondente à exceção.
   */
  private function mapErrorCode(Exception $exception): int {
    $statusCode = match (true) {
      $exception instanceof ValidationException     => 422,
      $exception instanceof AuthenticationException => 401,
      $exception instanceof AuthorizationException  => 403,
      $exception instanceof NotFoundHttpException   => 404,
      default                                       => 500,
    };

    return $statusCode;
  }
}
