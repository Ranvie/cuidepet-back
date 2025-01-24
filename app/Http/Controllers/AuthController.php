<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RecoveryRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Response\BusinessResponse;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ){}

    //TODO: muito importante, tratar o caso em que o usuário não existe no banco, uma vez que dá para notar a diferença de tempo na resposta
    //quando o email existe ou não no banco (por conta da conversão da hash)
    public function login(LoginRequest $request){
        $loginData = $request->validated();
        $token = $this->authService->login($loginData);

        $response = new BusinessResponse(200, $token);
        return $response->build();
    }

    public function register(RegisterRequest $request){
        $registerData = $request->validated();
        $user = $this->authService->register($registerData);

        $response = new BusinessResponse(200, $user);
        return $response->build();
    }

    public function recoveryPassword(RecoveryRequest $request){
        $recoveryData = $request->validated();
        $this->authService->recoveryPassword($recoveryData);

        $response = new BusinessResponse(200, "Um email de alteração de senha foi enviado, verifique sua caixa de entrada.");
        return $response->build();
    }

    public function resetPassword(ResetPasswordRequest $pwdRequest){
        $pwdData = $pwdRequest->validated();

        $this->authService->resetPassword($pwdData);
        $response = new BusinessResponse(200, "A senha de sua conta foi alterada com sucesso");
        return $response->build();
    }

    public function useTerms(){
        $this->authService->useTerms();
    }

    public function acceptTerms(){
        $this->authService->acceptTerms();
    }

    public function logout(int $userId){
        $this->authService->logout($userId);

        $response = new BusinessResponse(200, "Logout efetuado com sucesso");
        return $response->build();
    }
}
