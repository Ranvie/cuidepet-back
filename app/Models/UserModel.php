<?php

namespace App\Models;

use App\DTO\User\UserDTO;
use Illuminate\Auth\Authenticatable as TraitAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserModel extends BusinessModel implements Authenticatable{

  /**
   * Traits
   */
  use TraitAuthenticatable, HasApiTokens, HasFactory, Notifiable;

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = UserDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_user';

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

  /**
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public $fillable = [
    'username',
    'email',
    'password',
    'image_profile',
    'phone',
    'active',
    'email_verified_at'
  ];

  /**
   * Recupera um usuário pelo email
   * @param string $email
   * @param bool   $parse
   * @return UserDTO|UserModel|null
   */
  public function getByEmail(string $email, bool $parse = true) :UserDTO|UserModel|null {
    $user = $this->where('email',$email)->first();
    if(!$user) return null;
    if(!$parse) return $user;

    return $this->parser($user);
  }

  /**
   * Cria um novo usuário
   * @param array $data
   * @param array $relations
   * @param bool  $parse
   * @return UserDTO|UserModel
   */
  public function create(array $data, array $relations = [], bool $parse = true) :UserDTO|UserModel {
    parent::create($data, []);
    return parent::getById($this->original['id'], $relations, $parse);
  }

  //TODO:
  //Validar se não faz sentido deletar outros dados também
  //Inativar os formulários
  //Inativar os anúncios + animal + mídias + endereços dos anúncios (cuidado para não deletar os da newsletter)
  //Inativar as respostas
  //Inativar o histórico de respostas
  //Inativar as notificações
  //Inativar as preferências
  //Inativar as denúncias
  //Inativar os termos de uso aceitos
  //Inativar os favoritos
  
  /**
   * Inativa um usuário e seus dados relacionados, definindo o campo "active" como false. Retorna true se a operação for bem-sucedida.
   * @param  int|null $id
   * @return bool
   */
  public function inactivate($id = null) :bool {
    parent::edit($id, ['active' => 0]);
    return true;
  }

  /**
   * Define a relação entre um usuário e sua preferência. Um usuário possui uma preferência.
   * @return HasOne
   */
  public function preference() :HasOne {
    return $this->hasOne(PreferenceModel::class, 'user_id', 'id');
  }

  /**
   * Define a relação entre um usuários e notificações. Um usuário pode ter muitas notificações.
   * @return HasMany
   */
  public function notifications() :HasMany {
    return $this->hasMany(NotificationModel::class, 'user_id', 'id');
  }

  /**
   * Define a relação entre usuários e cargos. Um usuário pode ter muitos cargos (permissões).
   * @return BelongsToMany
   */
  public function roles() :BelongsToMany {
    return $this->BelongsToMany(RoleModel::class, UserRoleModel::class, 'user_id', 'role_id');
  }
  
  /**
   * Define a relação entre usuários e termos de uso aceitos. Um usuário pode aceitar muitos termos de uso.
   * @return BelongsToMany
   */
  public function useTerms() :BelongsToMany {
    return $this->BelongsToMany(UseTermsModel::class, UseTermsAcceptanceModel::class, 'user_id', 'use_terms_id');
  }

  /**
   * Define a relação entre usuários e anúncios favoritos. Um usuário pode ter muitos anúncios favoritos.
   * @return BelongsToMany
   */
  public function favorites() :BelongsToMany {
    return $this->belongsToMany(AnnouncementModel::class, FavoriteModel::class, 'user_id', 'announcement_id');
  }

  /**
   * Define a relação entre usuários e denúncias. Um usuário pode fazer muitas denúncias.
   * @return HasMany
   */
  public function reports() :HasMany {
    return $this->hasMany(ReportModel::class, 'user_id', 'id');
  }

  /**
   * Define a relação entre usuários e anúncios. Um usuário pode criar muitos anúncios.
   * @return HasMany
   */
  public function announcements() :HasMany {
    return $this->hasMany(AnnouncementModel::class, 'user_id', 'id');
  }

  /**
   * Define a relação entre usuários e formulários. Um usuário pode criar muitos formulários.
   * @return HasMany
   */
  public function forms() :HasMany {
    return $this->hasMany(FormModel::class, 'user_id', 'id');
  }

  /**
   * Define a relação entre usuários e respostas de formulários. Um usuário pode ter muitas respostas de formulários.
   * @return HasMany
   */
  public function responses() :HasMany {
    return $this->hasMany(FormResponseModel::class, 'user_id', 'id');
  }

  /**
   * Define a relação entre usuários e histórico de respostas de formulários. Um usuário pode criar muitas entradas no histórico de respostas de formulários.
   * @return HasMany
   */
  public function userResponseHistory(): HasMany{
    return $this->hasMany(UserResponseHistoryModel::class, 'user_id', 'id');
  }

  /**
   * Define a relação entre usuários e newsletter. Um usuário pode ter uma newsletter associada.
   * @return HasOne
   */
  public function newsletter() :HasOne {
    return $this->hasOne(NewsletterModel::class, 'user_id', 'id');
  }

}
