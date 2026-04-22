<?php

namespace App\Services\Interfaces;

use App\DTO\Token\TokenDTO;
use App\Exceptions\BusinessException;
use Doctrine\Common\Lexer\Token;

/**
 * Interface ITokenService
 * Define os métodos que devem ser implementados por qualquer serviço de token.
 */
interface ITokenService {

  /**
   * Cria um token para um contexto específico com um payload definido.
   * @param  string $context Contexto do token (ex. newsletter)
   * @param  array  $payload Dados a serem armazenados no token
   * @return string Hash do token criado
   */
  public function createToken(string $context, array $payload) :string;

  /**
   * Verifica a validade de um token para um contexto específico.
   * @param  string $context   Contexto do token (ex. newsletter)
   * @param  string $hash      Hash do token a ser verificado
   * @param  bool   $delete    Indica se o token deve ser deletado após a verificação
   * @return TokenDTO          Token encontrado e verificado
   * @throws BusinessException Se o token for inválido ou expirado
   */
  public function verifyToken(string $context, string $hash, bool $delete = true) :TokenDTO;

}