<?php

namespace App\Models;

use App\DTO\Address\AddressDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AddressModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = AddressDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_address';

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
    'integration_address_cache_id',
    'announcement_id',
    'street',
    'neighborhood',
    'number',
    'complement'
  ];

  /**
   * Define o relacionamento com o cache de endereços. Um endereço pertence a um cache de endereços.
   * @return BelongsTo
   */
  public function cacheAddress() :BelongsTo {
    return $this->belongsTo(IntegrationAddressCacheModel::class, 'integration_address_cache_id', 'id');
  }

  /**
   * Define o relacionamento com o anúncio. Um endereço pertence a um anúncio.
   * @return HasOne
   */
  public function announcement() :HasOne {
    return $this->hasOne(AnnouncementModel::class, 'id');
  }
}
