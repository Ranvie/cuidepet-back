<?php

namespace App\Models;

use App\DTO\Animal\AnimalDatabase;

class AnimalModel extends BusinessModel {

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = AnimalDatabase::class;

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_animal';

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
}
