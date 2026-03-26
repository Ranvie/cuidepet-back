<?php

namespace App\Models;

use App\DTO\Specie\SpecieDTO;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpecieModel extends BusinessModel {

	/**
	 * Define a classe de saída dos objetos. (Formato: Classe::class)
	 * @var string
	 */
	protected $class = SpecieDTO::class;

	/**
	 * Aponta a entidade do banco de dados
	 * @var string
	 */
	public $table = 'tb_specie';

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
	 * Define o relacionamento com as raças. Uma espécie pode ter várias raças.
	 * @return HasMany
	 */
	public function breeds() :HasMany {
		return $this->hasMany(BreedModel::class, 'specie_id');
	}

}
