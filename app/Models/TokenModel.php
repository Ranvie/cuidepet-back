<?php

namespace App\Models;

use App\DTO\Token\TokenDTO;

/**
 * Modelo de token, representando a entidade tb_token do banco
 */
class TokenModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = TokenDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_token';

  /**
   * Aponta a chave primária no banco de dados
   * @var string
   */
  public $primaryKey = 'id';

  /**
   * Define a chave primária como auto incremento
   * @var bool
   */
  public $incrementing = true;

  /**
   * Define campos created_at e updated_at gerenciados pelo láravel
   * @var bool
   */
  public $timestamps = false;

  /**
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public $fillable = [
    'type',
    'token',
    'payload',
    'expires_at',
  ];

}