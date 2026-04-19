<?php

namespace App\Models;

use App\DTO\FormResponse\FormResponseDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormResponseModel extends BusinessModel {
  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = FormResponseDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_form_response';

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
   * Inativa o campo updated_at, já que não é necessário para a entidade
   */
  const UPDATED_AT = null;

  /**
   * Recupera o anúncio relacionado à resposta do formulário. Uma resposta de formulário pertence a um anúncio.
   * @return BelongsTo
   */
  public function announcements() :BelongsTo {
    return $this->BelongsTo(AnnouncementModel::class, 'id', 'announcement_id');
  }

  /**
   * Recupera o usuário relacionado à resposta do formulário. Uma resposta de formulário pertence a um usuário.
   * @return BelongsTo
   */
  public function users() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'id', 'user_id');
  }
}
