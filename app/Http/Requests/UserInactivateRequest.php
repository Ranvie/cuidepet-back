<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para inativar um usuário, validando a senha fornecida.
 */
class UserInactivateRequest extends FormRequest {
  
  /**
   * Define se o usuário tem permissão na requisição
   * @return bool
   */
  public function authorize() :bool {
    return true;
  }

  /**
   * Regras de validação para a inativação de um usuário.
   * @return array
   */
  public function rules() :array {
    return [
      'password' => 'required|string|min:6'
    ];
  }
}
