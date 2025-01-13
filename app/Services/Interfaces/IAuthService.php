<?php

namespace App\Services\Interfaces;

interface IAuthService extends IService {
    public function login(string $email, string $password);
    public function register(string $name, string $email, string $password);
    public function logout();
}
