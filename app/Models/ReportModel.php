<?php

namespace App\Models;

use App\DTO\Report\ReportDTO;

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
    public $timestamps = false;

}
