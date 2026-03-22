<?php

namespace App\Models;

use App\DTO\ReportMessage\ReportMessageDTO;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportMessageModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = ReportMessageDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_report_message';

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

  public array $fillable = [
    'motive', 
    'type'
  ];

  /**
   * Define o relacionamento com os relatórios. Um tipo de denúncia pode estar em várias denúncias.
   * @return HasMany
   */
  public function reports() :HasMany {
    return $this->hasMany(ReportModel::class, 'report_message_id', 'id');
  }

}
