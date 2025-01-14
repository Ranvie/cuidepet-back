<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RecoveryRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ){}

    public function login(LoginRequest $request){
        $loginData = $request->validated();
        return $this->authService->login($loginData);
    }

    public function register(RegisterRequest $request){
        $registerData = $request->validated();
        $this->authService->register($registerData);
    }

    public function recoveryPassword(RecoveryRequest $request){
        $recoveryData = $request->validated();
        $this->authService->recoveryPassword($recoveryData);
    }

    public function useTerms(){
        $this->authService->useTerms();
    }

    public function acceptTerms(){
        $this->authService->acceptTerms();
    }

    public function logout(int $userId){
        return $this->authService->logout($userId);
    }
}
