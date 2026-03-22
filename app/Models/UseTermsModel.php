<?php

namespace App\Models;

use App\DTO\UseTerms\UseTermsDTO;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UseTermsModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = UseTermsDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_use_terms';

  /**
   * Aponta a chave primária no banco de dados
   * @var string
   */
  public string $primaryKey = 'id';

  /**
   * Define a chave primária como auto incremento
   * @var bool
   */
  public bool $incrementing = true;

  /**
   * Define campos created_at e updated_at gerenciados pelo láravel
   * @var bool
   */
  public bool $timestamps = true;

  /**
   * Define os campos que podem ser preenchidos em massa (mass assignment).
   * @var array
   */
  public array $fillable = ['title','description','active'];

  /**
   * Define o relacionamento muitos-para-muitos com os usuários e aceitação de termos de uso. Um aceite de termos de uso está associado a um usuário e a um termo de uso específico.
   * @return BelongsToMany
   */
  public function users() :BelongsToMany {
    return $this->belongsToMany(UserModel::class, UseTermsAcceptanceModel::class, 'use_terms_id', 'user_id');
  }

}