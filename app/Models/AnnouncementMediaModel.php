<?php

namespace App\Models;

use App\DTO\AnnouncementMedia\AnnouncementMediaDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementMediaModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = AnnouncementMediaDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_announcement_media';

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
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public array $fillable = [
    'announcement_id', 
    'url'
  ];

  /**
   * Define o relacionamento entre mídias e anúncios. Uma mídia pertence a um anúncio.
   * @return BelongsTo
   */
  public function announcement() :BelongsTo {
    return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
  }

}
