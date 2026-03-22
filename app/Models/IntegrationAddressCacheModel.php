<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\DTO\IntegrationAddressCache\IntegrationAddressCacheDTO;

class IntegrationAddressCacheModel extends BusinessModel {

  /**
   * Aponta a classe DTO associada a este modelo
   * @var string
   */
  protected string $class = IntegrationAddressCacheDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_integration_address_cache';

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
   * Desativa o campo created_at já que não é necessário para esta tabela
   */
  const CREATED_AT = null;

  /**
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public array $fillable = [
    'latitude',
    'longitude',
    'zipcode',
    'state',
    'city',
    'neighborhood',
    'street',
    'source'
  ];

  /**
   * Recupera as newsletters relacionadas a este cache de endereço. Um cache de endereço pode estar relacionado a muitas newsletters.
   * @return BelongsToMany
   */
  public function newsletters() :BelongsToMany {
    return $this->belongsToMany(
      NewsletterModel::class,
      NewsletterIntegrationAddressCacheModel::class,
      'integration_address_cache_id',
      'newsletter_id'
    );
  }

  /**
   * Recupera os endereços de anúncios relacionados a este cache de endereço. Um cache de endereço pode estar relacionado a muitos endereços de anúncios.
   * @return HasMany
   */
  public function announcementAddresses() :HasMany {
    return $this->hasMany(AddressModel::class, 'integration_address_cache_id', 'id');
  }

}