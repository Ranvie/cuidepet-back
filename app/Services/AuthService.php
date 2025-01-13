<?php

namespace App\Services;

use App\Exceptions\AuthException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Interfaces\IAuthService;

class AuthService
{
    public function login($data){
        dd("Login");
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

    public function logout(){
        dd("Logout");
    }
}
