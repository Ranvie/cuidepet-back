<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class BusinessResponse implements \JsonSerializable {

  /**
   * Código HTTP da resposta
   * @var int
   */
  public int $code = 200;

  /**
   * Conteúdo da resposta
   * @var array|object|string|bool
   */
  public array|object|string|bool $content;

  /**
   * Erros da resposta (opcional)
   * @var array
   */
  public array $errors = [];

  /**
   * Erros a serem adicionados à resposta (opcional, pode ser usado para acumular erros antes de construir a resposta)
   * @var array
   */
  public static ?array $addedErrors = null;

  /**
   * Construtor da resposta de negócio
   * @param int                      $code    Código HTTP da resposta
   * @param array|object|string|bool $content Conteúdo da resposta
   */
  public function __construct(int $code, object|array|string|bool $content) {
    $this->code        = $code;
    $this->content     = $content;
    $this->errors      = self::$addedErrors ?? [];
    self::$addedErrors = null;
  }

  /**
   * Constrói a resposta JSON a partir do conteúdo e código HTTP
   * @return JsonResponse Resposta JSON pronta para ser retornada ao cliente
   */
  public function build(): JsonResponse {
    return response()->json($this, $this->code);
  }

  /**
   * Adiciona um erro à resposta de negócio
   * @param  mixed $error Erro a ser adicionado
   * @return void
   */
  public static function addErrors(mixed $error): void {
    self::$addedErrors = array_merge(self::$addedErrors ?? [], is_array($error) ? $error : [$error]);
  }

  /**
   * Serializa a resposta para JSON, omitindo erros quando vazio
   * @return mixed
   */
  public function jsonSerialize(): mixed {
    $data = [
      'code'    => $this->code,
      'content' => $this->content,
    ];

    if (!empty($this->errors)) {
      $data['errors'] = $this->errors;
    }

    return $data;
  }
}
