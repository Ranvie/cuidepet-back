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
  protected string $class = NewsletterDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_newsletter';

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
    'user_id', 
    'email', 
    'email_confirmed'
  ];

  /**
   * Relacionamento com a entidade de usuário. Uma newsletter pertence a um usuário.
   * @return BelongsTo
   */
  public function user(): BelongsTo{
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

  /**
   * Relacionamento com a entidade de cache de endereço. Uma newsletter pode estar relacionada a muitos caches de endereço.
   * @return BelongsToMany
   */
  public function addresses(): BelongsToMany{
    return $this->belongsToMany(
      IntegrationAddressCacheModel::class, 
      NewsletterIntegrationAddressCacheModel::class, 
      'newsletter_id', 
      'integration_address_cache_id'
    );
  }

}
