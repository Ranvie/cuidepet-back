<?php

namespace App\Models;

use App\DTO\Report\ReportDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = ReportDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_report';

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
  public array $fillable = ['description'];

  /**
   * Define o relacionamento com o usuário. Uma denúncia pertence a um usuário.
   * @return BelongsTo
   */
  public function user() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

  /**
   * Define o relacionamento com o anúncio. Uma denúncia pertence a um anúncio.
   * @return BelongsTo
   */
  public function announcement() :BelongsTo {
    return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
  }

  /**
   * Define o relacionamento com o formulário. Uma denúncia pertence a um formulário.
   * @return BelongsTo
   */
  public function form() :BelongsTo {
    return $this->belongsTo(FormModel::class, 'form_id', 'id');
  }

  /**
   * Define o relacionamento com a mensagem de relatório. Uma denúncia pertence a uma mensagem de relatório.
   * @return BelongsTo
   */
  public function reportMessage() :BelongsTo {
    return $this->belongsTo(ReportMessageModel::class, 'report_message_id', 'id');
  }

}
