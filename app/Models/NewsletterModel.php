<?php

namespace App\Models;

use App\DTO\Newsletter\NewsletterDTO;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = NewsletterDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_newsletter';

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

  public $fillable = ['user_id', 'email', 'email_confirmed'];

  public function user(): BelongsTo{
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

  public function addresses(): BelongsToMany{
    return $this->belongsToMany(
      IntegrationAddressCacheModel::class, 
      NewsletterIntegrationAddressCacheModel::class, 
      'newsletter_id', 
      'integration_address_cache_id'
    );
  }

}
