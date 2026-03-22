<?php

namespace App\Models;

use App\DTO\Notification\NotificationDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = NotificationDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_notification';

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
   * Define o campo updated_at como nulo, já que não é necessário para a entidade
   */
  const UPDATED_AT = null;

  /**
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public array $fillable = ['viewed'];

  /**
   * Relacionamento com a entidade de usuário. Uma notificação pertence a um usuário.
   * @return BelongsTo
   */
  public function user() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

  /**
   * Relacionamento com a entidade de template de notificação. Uma notificação pertence a um template de notificação.
   * @return BelongsTo
   */
  public function notificationTemplate() :BelongsTo {
    return $this->belongsTo(NotificationTemplateModel::class, 'notification_template_id', 'id');
  }

}
