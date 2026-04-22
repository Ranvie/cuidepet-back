<?php

namespace App\Models;

use App\DTO\UseTerms\UseTermsDTO;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UseTermsModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = UseTermsDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_use_terms';

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
  public $timestamps = true;

  /**
   * Define os campos que podem ser preenchidos em massa (mass assignment).
   * @var array
   */
  public $fillable = ['title','description','active'];

  /**
   * Busca o termo de uso mais recente
   * @param  bool $parse                    Indica se o resultado deve ser processado pela função parser (padrão: true)
   * @return UseTermsModel|UseTermsDTO|null Retorna o termo de uso mais recente ou null se não houver nenhum.
   */
  public function getLatestUseTerms(bool $parse = true) :?object {
    $useTerms = $this->query()
      ->orderBy('created_at', 'desc')
      ->where('active', true)
      ->first();

    if($useTerms instanceof UseTermsModel && $parse)
      $useTerms = $this->parser($useTerms);

    return $useTerms;
  }

  /**
   * Define o relacionamento muitos-para-muitos com os usuários e aceitação de termos de uso. Um aceite de termos de uso está associado a um usuário e a um termo de uso específico.
   * @return BelongsToMany
   */
  public function users() :BelongsToMany {
    return $this->belongsToMany(UserModel::class, UseTermsAcceptanceModel::class, 'use_terms_id', 'user_id');
  }

}