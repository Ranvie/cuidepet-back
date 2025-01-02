<?php

namespace App\Models;

use App\DTO\User\UserDatabase;

class UserModel extends BusinessModel {

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = UserDatabase::class;

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

}
