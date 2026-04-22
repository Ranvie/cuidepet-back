<?php

namespace App\DTO\Token;

/**
 * DTO (Data Transfer Object) para representar os dados de um token, utilizado em operações como
 * confirmação de e-mail, redefinição de senha, etc.
 */
class TokenDTO {
  
  /**
   * Identificador do token
   * @var int
   */
  public int $id;

  /**
   * Tipo do token (ex: 'subscribe-newsletter', etc)
   * @var string
   */
  public string $type;

  /**
   * Token em si, geralmente um hash aleatório
   * @var string
   */
  public string $token;

  /**
   * Payload associado ao token, pode conter informações adicionais
   * @var string
   */
  public string $payload;

  /**
   * Data de expiração do token
   * @var string
   */
  public string $expiresAt;

}