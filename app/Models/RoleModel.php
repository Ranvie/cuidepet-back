<?php

namespace App\Models;

use App\DTO\Role\RoleDTO;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected string $class = RoleDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public string $table = 'tb_role';

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
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public array $fillable = [
    'name', 
    'description'
  ];

  /**
   * Define o relacionamento com os usuários. Um papel pode estar em vários usuários.
   * @return BelongsToMany
   */
  public function roles() :BelongsToMany {
    return $this->belongsToMany(UserModel::class, UserRoleModel::class, 'user_id', 'role_id');
  }

}
