<?php

namespace App\Models;

use App\DTO\Specie\SpecieDTO;
use App\Models\BusinessModel;

//TODO: BreedSpecieModel tá meio bizarra, a ideia dele é trazer a spécie + relações com raças (possivelmente para o dropdown de criação do anúncio)
//dar uma validada nisso

use Illuminate\Database\Eloquent\Relations\HasMany;

class BreedSpecieModel extends BusinessModel {
  
  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = SpecieDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_specie';

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
   * Define a relação entre espécies e raças. Uma espécie pode ter várias raças, mas uma raça pertence a apenas uma espécie.
   * @return HasMany
   */
  public function breed() :HasMany {
    return $this->hasMany(BreedModel::class, 'specie_id', 'id');
  }
}
