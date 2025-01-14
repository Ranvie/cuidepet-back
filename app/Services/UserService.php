<?php

namespace App\Services;

use App\DTO\User\UserDTO;
use App\Exceptions\BusinessException;
use App\Models\UserModel;
use App\Services\Interfaces\IUserService;

class UserService implements IUserService
{
    public function __construct(
        private UserModel $userModel,
        private FormService $formService
    ){}

    public function getList($limit, $page) {
        return $this->userModel->list($limit, $page);
    }

    public function getById($id, $relations = ['preference', 'roles', 'forms'], $parse = true) :UserDTO|UserModel {
        $user = $this->userModel->getById($id, $relations, $parse);

        if(!$user)
            throw new BusinessException('O usuário não foi encontrado.', 404);

        return $user;
    }

    public function getByEmail($email, $parse = true) {
        $user = $this->userModel->getByEmail($email, $parse);

        if(!$user)
            throw new BusinessException('O email não foi encontrado.', 404);

        return $user;
    }

    public function create($data, $relations = [], $parse = true) :UserDTO|UserModel {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = $this->userModel->create($data, [], false);
        $user->preference()->create();
        $user->roles()->sync([2]);

        $userId = $user->getOriginal()['id'];
        $this->formService->create(['userId' => $userId]);

        return $this->userModel->getById($userId, ['preference', 'roles', 'forms']);
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
