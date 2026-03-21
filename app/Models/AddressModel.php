<?php

namespace App\Models;

use App\DTO\UseTerms\UseTermsDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = UseTermsDTO::class;

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

  public $fillable = ['integration_address_cache_id', 'announcement_id', 'street','neighborhood', 'number', 'complement'];

  public function cacheAddresses() :BelongsTo {
    return $this->belongsTo(IntegrationAddressCacheModel::class, 'integration_address_cache_id', 'id');
  }

  public function announcement() :BelongsTo {
    return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
  }
}
