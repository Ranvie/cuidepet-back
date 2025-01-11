<?php

namespace App\Services;

use App\DTO\User\UserDTO;
use App\Exceptions\BusinessException;
use App\Models\UserModel;
use App\Services\Interfaces\IUserService;

class UserService implements IUserService
{
    public function __construct(private UserModel $userModel){}

    public function getList($limit, $page) {
        return $this->userModel->list($limit, $page);
    }

    public function getById($id, $relations = ['preference', 'roles']) :UserDTO {
        $user = $this->userModel->getById($id, $relations);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 404);

        return $user;
    }

    public function create($data) {
        return $this->userModel->create($data);
    }

    public function edit($id, $data)
    {
        $user = $this->userModel->edit($id, $data);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 404);

        return $user;
    }

    public function remove($id = null)
    {
        return $this->userModel->remove($id);
    }

    public function inactivate($id = null)
    {
        return $this->userModel->inactivate($id);
    }
}
