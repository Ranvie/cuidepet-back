<?php

namespace App\Models;

use App\DTO\Report\ReportDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = ReportDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_report';

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
   * Define o campo updated_at como nulo, já que não é necessário para a entidade
   */
  const UPDATED_AT = null;

  public $fillable = ['description'];

  public function user() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

  public function announcement() :BelongsTo {
    return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
  }

  public function form() :BelongsTo {
    return $this->belongsTo(FormModel::class, 'form_id', 'id');
  }

  public function reportMessage() :BelongsTo {
    return $this->belongsTo(ReportMessageModel::class, 'report_message_id', 'id');
  }

}
