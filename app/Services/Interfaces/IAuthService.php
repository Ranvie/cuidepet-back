<?php

namespace App\Services\Interfaces;

use App\DTO\User\UserDTO;
use App\Models\UserModel;

interface IAuthService extends IService {

  /**
   * Realiza o login de um usuário.
   * @param  string $email    Email do usuário.
   * @param  string $password Senha do usuário.
   * @return array            Dados do usuário autenticado e token de acesso.
   */
  public function login(string $email, string $password) :array;
  
  /**
   * Realiza o registro de um novo usuário.
   * @param  string $name     Nome do usuário.
   * @param  string $email    Email do usuário.
   * @param  string $password Senha do usuário.
   * @return array            Dados do usuário registrado e token de acesso.
   */
  public function register(string $name, string $email, string $password) :UserDTO|UserModel;
  
  /**
   * Realiza o logout do usuário.
   * @return bool Indica se o logout foi bem-sucedido.
   */
  public function logout() :bool;
}
