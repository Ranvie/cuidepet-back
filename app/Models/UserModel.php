<?php

namespace App\Models;

use App\DTO\User\UserDTO;
use App\Services\FormService;
use Illuminate\Auth\Authenticatable as TraitAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserModel extends BusinessModel implements Authenticatable{

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

    public $fillable = ['username','email','password','image_profile','phone','active','created_at','updated_at'];

    public function getByEmail($email, $parse) {
        $user = $this->where('email',$email)->first();
        if(!$user) return null;
        if(!$parse) return $user;

        return $this->parser($user);
    }

    public function create($data, $relations = [], $parse = true)
    {
        parent::create($data, []);
        return parent::getById($this->original['id'], $relations, $parse);
    }

    //Validar se não faz sentido deletar outros dados também
    public function inactivate($id = null): bool{
        parent::edit($id, ['active' => 0]);
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

    public function responses(): HasMany{
        return $this->hasMany(FormResponseModel::class, 'user_id', 'id');
    }

}
