<?php

namespace App\Services;

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

        $response = new BusinessResponse(200, $token);
        return $response->build();
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
        dd("Register");
    }

    public function recoveryPassword($data){
        dd("RecoveryPassword");
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
        $response = new BusinessResponse(200, "Logout efetuado com sucesso");
        return $response->build();
    }

    private function deleteTokens($user){
        $user->tokens()->delete();
    }
}
