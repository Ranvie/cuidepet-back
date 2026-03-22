<?php

namespace App\Models;

class NewsletterIntegrationAddressCacheModel extends BusinessModel {

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_newsletter_integration_address_cache';

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

}
