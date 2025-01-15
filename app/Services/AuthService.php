<?php

namespace App\Services;

use App\Events\RecoverPasswordEvent;
use App\Exceptions\BusinessException;
use App\Http\Response\BusinessResponse;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthService
{
    public function __construct(
        private UserService $userService
    ){}

    public function login($data){
        $user = $this->validateUser($data);
        $this->deleteTokens($user);

        $token = new stdClass();
        $token->token = $user->createToken($user->username.'-AuthToken')->plainTextToken;

        return $token;
    }

    private function validateUser($data): UserModel{
        $user = null;

        try{
            $user = $this->userService->getByEmail($data['email'], false);

            if(!Hash::check($data['password'], $user->password))
                throw new BusinessException();

        }catch (BusinessException $e){
            throw new BusinessException('Verifique suas credenciais e tente novamente', 400);
        }

        return $user;
    }

    public function register($data){
        return $this->userService->create($data);
    }

    public function recoveryPassword($data){
        $user = $this->userService->getByEmail($data['email']);

        RecoverPasswordEvent::dispatch($user, 'teste.com.br');

    }

    public function useTerms(){
        dd("UseTherms");
    }

    public function acceptTerms(){
        dd("AcceptTherms");
    }

    public function logout(int $userId){
        $user = $this->userService->getById($userId, [], false);

        $this->deleteTokens($user);
        return true;
    }

    private function deleteTokens($user){
        $user->tokens()->delete();
    }
}
