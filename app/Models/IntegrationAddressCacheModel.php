<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegrationAddressCacheModel extends BusinessModel {

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_integration_address_cache';

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
   * Desativa o campo created_at já que não é necessário para esta tabela
   */
  const CREATED_AT = null;

  public $fillable = ['latitude', 'longitude', 'zipcode', 'state', 'city', 'neighborhood', 'street', 'source'];

  public function newsletters(): BelongsToMany{
    return $this->belongsToMany(
      NewsletterModel::class,
      NewsletterIntegrationAddressCacheModel::class,
      'integration_address_cache_id',
      'newsletter_id'
    );
  }

  public function announcementAddresses(): HasMany {
    return $this->hasMany(AddressModel::class, 'integration_address_cache_id', 'id');
  }

}