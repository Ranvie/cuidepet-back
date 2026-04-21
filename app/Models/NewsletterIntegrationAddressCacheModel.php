<?php

namespace App\Models;

use App\DTO\NewsletterIntegrationAddressCache\NewsletterIntegrationAddressCacheDTO;

/**
 * Modelo responsável por representar a tabela de cache de endereços para integração da newsletter.
 * Esta tabela armazena os códigos postais associados às newsletters para facilitar a resolução de destinatários
 * com base na localização.
 */
class NewsletterIntegrationAddressCacheModel extends BusinessModel {

  /**
   * Aponta a classe DTO associada a este modelo
   * @var string
   */
  protected $class = NewsletterIntegrationAddressCacheDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_newsletter_integration_address_cache';

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
   * Define o relacionamento com a newsletter
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function newsletter() {
    return $this->belongsTo(NewsletterModel::class, 'newsletter_id', 'id');
  }

}
