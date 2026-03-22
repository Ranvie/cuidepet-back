<?php

namespace App\Models;

use App\DTO\Favorite\FavoriteDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteModel extends BusinessModel {

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_favorite';

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
  public bool $timestamps = false;

  /**
   * Relacionamento com a entidade de anúncio. Um favorito pertence a um anúncio.
   * @return BelongsTo
   */
  public function announcement() :BelongsTo {
    return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
  }

  /**
   * Relacionamento com a entidade de usuário. Um favorito pertence a um usuário.
   * @return BelongsTo
   */
  public function user() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

}
