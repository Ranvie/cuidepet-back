<?php

namespace App\Models;

use App\DTO\Animal\AnimalDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = AnimalDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_animal';

  /**
   * Aponta a chave primária no banco de dados
   * @var string
   */
  public $primaryKey = 'announcement_id';

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
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public $fillable = [
    'announcement_id',
    'name',
    'gender',
    'color',
    'size',
    'age',
    'disability',
    'vaccinated',
    'dewormed',
    'castrated',
    'image_profile',
    'last_seen_date',
    'breed_id'
  ];

  /**
   * Criação de um novo registro no banco de dados, utilizando os dados fornecidos. Retorna o objeto criado.
   * @param array $data
   * @param array $relations
   * @param bool $parse
   * @return AnimalDTO
   */
  public function create(array $data, array $relations = [], bool $parse = true) :AnimalDTO {
    parent::create($data, $relations, $parse);
    return parent::getById($this->original['announcement_id'], ['breed', 'species']);
  }

  /**
   * Define o relacionamento entre os modelos de Animal e Anúncio. Um animal pertence a um anúncio.
   * @return BelongsTo
   */
  public function announcement() :BelongsTo {
    return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
  }

  /**
   * Define o relacionamento entre os modelos de Animal e Raça. Um animal pertence a uma raça.
   * @return BelongsTo
   */
  public function breed() :BelongsTo {
    return $this->belongsTo(BreedModel::class, 'breed_id', 'id');
  }
}
