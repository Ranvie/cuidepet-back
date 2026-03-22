<?php

namespace App\Models;

use App\DTO\NotificationTemplate\NotificationTemplateDTO;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplateModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = NotificationTemplateDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_notification_template';

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
    'title', 
    'type', 
    'message'
  ];

  /**
   * Define o relacionamento entre template de notificação e notificações. Um template de notificação pode ter muitas notificações.
   * @return HasMany
   */
  public function notifications() :HasMany {
    return $this->hasMany(NotificationModel::class, 'notification_template_id', 'id');
  }

}
