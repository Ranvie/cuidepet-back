<?php

namespace App\Exceptions;

use App\Http\Response\BusinessResponse;
use InvalidArgumentException;
use stdClass;
use Exception;
use Throwable;
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
   * @var Throwable
   */
  private Throwable $exception;

  /**
   * Constrói um manipulador de exceções de negócio.
   * @param Throwable $exception A exceção a ser manipulada.
   */
  public function __construct(Throwable $exception) {
    $this->exception = $exception;
    $this->status    = $this->mapErrorCode($this->exception);
  }

  /**
   * Renderiza a resposta de erro para a exceção de negócio.
   * @return JsonResponse A resposta JSON contendo a mensagem de erro e, se aplicável, os detalhes dos erros de validação.
   */
  public function render(): JsonResponse {
    $debug = config('app.debug', env('APP_DEBUG', false));
  
    $responseData          = new stdClass();
    $responseData->message = $debug ? $this->exception->getMessage() : $this->defaultErrorMessages($this->status);
    
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
  private function mapErrorCode(Throwable $exception): int {
    $statusCode = match (true) {
      $exception instanceof AuthenticationException  => 401,
      $exception instanceof AuthorizationException   => 403,
      $exception instanceof NotFoundHttpException    => 404,
      $exception instanceof ValidationException      => 422,
      $exception instanceof InvalidArgumentException => 400,
      default                                        => 500,
    };

    return $statusCode;
  }

  /**
   * Fornece mensagens de erro padrão com base no código de status HTTP.
   * @param  int $statusCode O código de status HTTP para o qual a mensagem de erro deve ser fornecida.
   * @return string          A mensagem de erro correspondente ao código de status HTTP.
   */
  private function defaultErrorMessages($statusCode) :string {
    $messages =  [
      401 => 'Não autenticado',
      403 => 'Acesso negado',
      404 => 'Recurso não encontrado',
      422 => 'Erro de validação',
      500 => 'Erro interno do servidor',
    ];

    return $messages[$statusCode] ?? $messages[500];
  }
}
