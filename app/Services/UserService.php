<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\UserModel;
use App\Services\Interfaces\IUserService;

class UserService implements IUserService
{
    public function __construct(private UserModel $userModel){}

    //TODO: não deve retornar usuários inativos;
    public function getList($limit, $page)
    {
        return $this->userModel->getPaginated($limit, $page);
    }

    //TODO: não deve retornar usuários inativos;
    public function getById($id, $relations = [])
    {
        $user = $this->userModel->getById($id, $relations);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 200);

        return $user;
    }

    public function create($data)
    {
        return $this->userModel->create($data);
    }

    //TODO: não deve editar usuários inativos;
    public function edit($id, $data)
    {
        $user = $this->userModel->edit($id, $data);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 200);

        return $user;
    }

    public function remove($id = null)
    {
        return $this->userModel->remove($id);
    }
}
