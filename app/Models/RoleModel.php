<?php

namespace App\Models;

use App\DTO\Role\RoleDTO;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleModel extends BusinessModel {

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = RoleDTO::class;

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_role';

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

    public $fillable = ['id', 'name', 'description'];

    public function roles(): BelongsToMany{
        return $this->BelongsToMany(UserModel::class, UserRoleModel::class, 'user_id', 'role_id');
    }

}
