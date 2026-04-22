<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\Token\TokenDTO;
use App\Exceptions\BusinessException;
use App\Models\TokenModel;
use App\Services\Interfaces\ITokenService;
use App\Utils\Functions;

/**
 * Serviço responsável por gerenciar as operações relacionadas aos tokens, incluindo
 * criação, verificação e exclusão de tokens em diferentes contextos (ex. newsletter).
 */
class TokenService implements ITokenService {

  /**
   * Método Construtor
   * @param TokenModel $tokenModel
   */
  public function __construct(
    private TokenModel $tokenModel
  ) {}

  /**
   * Verifica a validade de um token com base em seu contexto e hash, e opcionalmente o exclui após a verificação.
   * @param  string   $context Contexto do token (ex. newsletter)
   * @param  string   $hash    Hash do token a ser verificado
   * @param  bool     $delete  Indica se o token deve ser excluído após a verificação
   * @return TokenDTO          Modelo do token encontrado
   */
  public function verifyToken(string $context, string $hash, bool $delete = true) :TokenDTO {
    $obToken   = $this->tokenModel->getByQuery([new Filter('type', '=', $context), new Filter('token', '=', $hash)], [], true);
    $isExpired = $this->isExpired($obToken);

    if($obToken && ($delete || $isExpired))
      $this->tokenModel->delete($obToken->id);

    if(!$obToken instanceof TokenDTO || $isExpired)
      throw new BusinessException('Token inválido ou expirado.', 400);
    
    return $obToken;
  }

  /**
   * Verifica se um token está expirado com base em sua data de expiração.
   * @param  TokenDTO|null $obToken Modelo do token a ser verificado
   * @return bool                   Retorna true se o token estiver expirado, caso contrário false
   */
  private function isExpired(?TokenDTO $obToken) :bool {
    if(!$obToken instanceof TokenDTO)
      return true;

    if(empty($obToken->expiresAt))
      return false;

    $expiresAt = strtotime($obToken->expiresAt);
    return time() > $expiresAt;
  }

  /**
   * Cria um token para um contexto específico com um payload associado.
   * @param  string   $context          Contexto do token (ex. newsletter)
   * @param  array    $payload          Dados associados ao token
   * @param  int|null $expiresInMinutes Tempo em minutos para expiração do token (opcional)
   * @return string                     Hash do token criado
   */
  public function createToken(string $context, array $payload, ?int $expiresInMinutes = null) :string {
    $obToken = $this->tokenModel->create([
      'type'       => $context,
      'token'      => Functions::getRandomHash(64),
      'payload'    => json_encode($payload),
      'expires_at' => $expiresInMinutes ? date('Y-m-d H:i:s', strtotime("+$expiresInMinutes minutes")) : null,
    ]);

    return $obToken->token;
  }

}