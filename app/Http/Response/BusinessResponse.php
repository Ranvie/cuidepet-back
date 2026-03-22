<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class BusinessResponse {

  /**
   * Código HTTP da resposta
   * @var int
   */
  public int $code = 200;

  /**
   * Conteúdo da resposta
   * @var array|object|string
   */
  public array|object|string $content;

  /**
   * Construtor da resposta de negócio
   * @param int                 $code    Código HTTP da resposta
   * @param array|object|string $content Conteúdo da resposta
   */
  public function __construct(int $code, object|array|string $content) {
    $this->code = $code;
    $this->content = $content;
  }

  /**
   * Constrói a resposta JSON a partir do conteúdo e código HTTP
   * @return JsonResponse Resposta JSON pronta para ser retornada ao cliente
   */
  public function build(): JsonResponse {
    return response()->json($this, $this->code);
  }
}
