<?php

namespace App\Models;

use App\DTO\Breed\BreedDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BreedModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = BreedDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_breed';

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
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public $fillable = ['name'];

  /**
   * Define o relacionamento entre raça e animais. Uma raça pode estar em vários animais.
   * @return HasMany
   */
  public function animals() :HasMany {
    return $this->hasMany(AnimalModel::class, 'breed_id', 'id');
  }

  /**
   * Define o relacionamento entre raça e espécie. Uma raça pertence a uma espécie.
   * @return BelongsTo
   */
  public function specie() :BelongsTo {
    return $this->belongsTo(SpecieModel::class, 'specie_id', 'id');
  }

}
