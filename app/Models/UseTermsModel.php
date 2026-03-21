<?php

namespace App\Models;

use App\DTO\Breed\BreedDTO;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UseTermsModel extends BusinessModel {

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = BreedDTO::class;

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_use_terms';

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

    public function users() :BelongsToMany {
        return $this->belongsToMany(UserModel::class, UseTermsAcceptanceModel::class, 'use_term_id', 'user_id');
    }

}
