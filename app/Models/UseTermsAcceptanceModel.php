<?php

namespace App\Models;

use App\DTO\UseTermsAcceptance\UseTermsAcceptanceDTO;

class UseTermsAcceptanceModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  public $class = UseTermsAcceptanceDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_use_terms_acceptance';

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
  public $fillable = ['use_terms_id', 'user_id', 'accepted_at'];

}
