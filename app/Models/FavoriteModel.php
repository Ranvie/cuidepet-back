<?php

namespace App\Models;

use App\DTO\Favorites\FavoriteDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteModel extends BusinessModel {

  /**
   * DTO de favoritos
   * @var string
   */
  public $class = FavoriteDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_favorite';

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
    'user_id',
    'announcement_id'
  ];

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
