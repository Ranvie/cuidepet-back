<?php

namespace App\Models;

use App\DTO\User\UserDTO;
use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserModel extends BusinessModel {

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

    public $fillable = ['username','email','password','image_profile','phone','active','created_at','updated_at'];

    public function getById($id, $relations = ['roles', 'preference', 'notifications']){
        $user = parent::getById($id, $relations);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 200);

        return $user;
    }

    public function create($data, $relations = ['roles'])
    {
        return parent::create($data, $relations);
    }

    public function edit($id, $data, $ignoreNulls = true)
    {
        $user = parent::edit($id, $data, $ignoreNulls);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 200);

        return $user;
    }

    //TODO: Implementar o update no campo active do usuário, setando como false;
    //Validar se não faz sentido deletar outros dados também
    public function remove($id = null): bool{
        return true;
    }

    public function preference(): HasOne{
        return $this->hasOne(PreferenceModel::class, 'user_id', 'id');
    }

    public function notifications(): HasMany{
        return $this->hasMany(NotificationModel::class, 'user_id', 'id');
    }

    public function roles(): BelongsToMany{
        return $this->BelongsToMany(RoleModel::class, UserRoleModel::class, 'user_id', 'role_id');
    }

    public function favorites(): HasMany{
        return $this->hasMany(FavoriteModel::class, 'user_id', 'id');
    }

    public function reports(): HasMany{
        return $this->hasMany(ReportModel::class, 'user_id', 'id');
    }

    public function announcements(): HasMany{
        return $this->hasMany(AnnouncementModel::class, 'user_id', 'id');
    }

    public function forms(): HasMany{
        return $this->hasMany(FormModel::class, 'user_id', 'id');
    }

}
