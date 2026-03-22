<?php

namespace App\Models;

use App\DTO\UserResponseHistory\UserResponseHistoryDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserResponseHistoryModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = UserResponseHistoryDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_user_response_history';

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
   * Inativa o campo updated_at do laravel, pois não é necessário para a entidade.
   */
  const UPDATED_AT = null;

  /**
   * Define os campos que podem ser preenchidos em massa (mass assignment).
   * @var array
   */
  public array $fillable = ['expires_at'];

  /**
   * Define relação entre histórico de respostas do usuário e anúncios. Um histórico de resposta pertence a um anúncio.
   * @return BelongsTo
   */
  public function announcements() :BelongsTo {
    return $this->BelongsTo(AnnouncementModel::class, 'id', 'announcement_id');
  }

  /**
   * Define relação entre histórico de respostas e usuários. Um histórico de resposta pertence a um usuário.
   * @return BelongsTo
   */
  public function users() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'id', 'user_id');
  }

}
