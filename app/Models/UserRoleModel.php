<?php

namespace App\Models;

class UserRoleModel extends BusinessModel {

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_user_role';

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
