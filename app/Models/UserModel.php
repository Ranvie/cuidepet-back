<?php

namespace App\Models;

use App\DTO\User\UserDTO;
use App\Exceptions\BusinessException;

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

    public $fillable = ['username','email','secondary_email','password','image_profile','main_phone','secondary_phone','active','created_at','updated_at'];

    public function getById($id){
        $user = parent::getById($id);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 200);

        return $user;
    }

    //TODO: Implementar o update no campo active do usuário, setando como false;
    //Validar se não faz sentido deletar outros dados também
    public function remove($id): bool{
        return true;
    }
}
